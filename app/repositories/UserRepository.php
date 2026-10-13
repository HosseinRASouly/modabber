<?php
declare(strict_types=1);

/**
 * Modabber User Repository
 */

class UserRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByUsernameOrEmail(string $identifier): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE (username = :ident OR email = :ident) LIMIT 1");
        $stmt->execute([':ident' => $identifier]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = :now, updated_at = :now WHERE id = :id");
        $stmt->execute([':id' => $id, ':now' => date('Y-m-d H:i:s')]);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = :hash, updated_at = :now WHERE id = :id");
        $stmt->execute([':id' => $id, ':hash' => $passwordHash, ':now' => date('Y-m-d H:i:s')]);
    }

    public function createAdmin(string $username, string $fullName, ?string $email, string $passwordHash): int
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("INSERT INTO users (username, full_name, email, password_hash, role, is_active, created_at, updated_at) VALUES (:u, :fn, :em, :hash, 'admin', 1, :now, :now)");
        $stmt->execute([
            ':u'    => $username,
            ':fn'   => $fullName,
            ':em'   => $email,
            ':hash' => $passwordHash,
            ':now'  => $now,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function countFailedAttempts(string $ip, string $identifier, int $secondsWindow): int
    {
        $since = date('Y-m-d H:i:s', time() - $secondsWindow);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = :ip AND identifier = :ident AND created_at >= :since");
        $stmt->execute([':ip' => $ip, ':ident' => $identifier, ':since' => $since]);
        return (int)$stmt->fetchColumn();
    }

    public function countFailedAttemptsByIp(string $ip, int $secondsWindow): int
    {
        $since = date('Y-m-d H:i:s', time() - $secondsWindow);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = :ip AND created_at >= :since");
        $stmt->execute([':ip' => $ip, ':since' => $since]);
        return (int)$stmt->fetchColumn();
    }

    public function recordLoginAttempt(string $ip, string $identifier): void
    {
        $stmt = $this->db->prepare("INSERT INTO login_attempts (ip_address, identifier, created_at) VALUES (:ip, :ident, :now)");
        $stmt->execute([':ip' => $ip, ':ident' => $identifier, ':now' => date('Y-m-d H:i:s')]);
    }

    public function clearLoginAttempts(string $ip, string $identifier): void
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE ip_address = :ip OR identifier = :ident");
        $stmt->execute([':ip' => $ip, ':ident' => $identifier]);
    }

    public function cleanupLoginAttempts(int $secondsOlderThan): void
    {
        $cutoff = date('Y-m-d H:i:s', time() - $secondsOlderThan);
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE created_at < :cutoff");
        $stmt->execute([':cutoff' => $cutoff]);
    }
}
