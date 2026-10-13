<?php
declare(strict_types=1);

/**
 * Modabber Form Submission Audit & Rate-Limit Repository
 *
 * NOTE: For privacy and security, this table stores strictly NO personal data:
 * NO names, NO phone numbers, NO messages, and NO uploaded files.
 */

class FormSubmissionRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function log(string $ip, int $projectId, string $status = 'pending'): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO form_submissions (ip_address, project_id, status, created_at)
             VALUES (:ip, :pid, :st, :now)"
        );
        $stmt->execute([
            ':ip'  => $ip,
            ':pid' => $projectId,
            ':st'  => $status,
            ':now' => date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare("UPDATE form_submissions SET status = :st WHERE id = :id");
        $stmt->execute([':st' => $status, ':id' => $id]);
    }

    public function countByIpInWindow(string $ip, int $secondsWindow): int
    {
        $since = date('Y-m-d H:i:s', time() - $secondsWindow);
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM form_submissions WHERE ip_address = :ip AND created_at >= :since"
        );
        $stmt->execute([':ip' => $ip, ':since' => $since]);
        return (int)$stmt->fetchColumn();
    }

    public function cleanup(int $secondsOlderThan = 172800): void
    {
        $cutoff = date('Y-m-d H:i:s', time() - $secondsOlderThan);
        $stmt = $this->db->prepare("DELETE FROM form_submissions WHERE created_at < :cutoff");
        $stmt->execute([':cutoff' => $cutoff]);
    }
}
