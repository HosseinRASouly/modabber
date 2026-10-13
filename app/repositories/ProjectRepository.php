<?php
declare(strict_types=1);

/**
 * Modabber Project Repository
 */

class ProjectRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getPublished(int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM projects WHERE is_published = 1 ORDER BY sort_order ASC, created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countPublished(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM projects WHERE is_published = 1");
        return (int)$stmt->fetchColumn();
    }

    public function getAll(int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM projects ORDER BY sort_order ASC, created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM projects");
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getPublishedById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = :id AND is_published = 1 LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare(
            "INSERT INTO projects (name, short_description, full_description, budget, image, is_published, is_expired, sort_order, created_at, updated_at)
             VALUES (:name, :short_desc, :full_desc, :budget, :image, :published, :expired, :sort_order, :created, :updated)"
        );

        $stmt->execute([
            ':name'       => $data['name'],
            ':short_desc' => $data['short_description'],
            ':full_desc'  => $data['full_description'],
            ':budget'     => $data['budget'],
            ':image'      => $data['image'] ?? null,
            ':published'  => $data['is_published'] ? 1 : 0,
            ':expired'    => !empty($data['is_expired']) ? 1 : 0,
            ':sort_order' => (int)($data['sort_order'] ?? 0),
            ':created'    => $now,
            ':updated'    => $now,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE projects SET name = :name, short_description = :short_desc, full_description = :full_desc,
                budget = :budget, is_published = :published, is_expired = :expired, sort_order = :sort_order, updated_at = :updated";

        $params = [
            ':id'         => $id,
            ':name'       => $data['name'],
            ':short_desc' => $data['short_description'],
            ':full_desc'  => $data['full_description'],
            ':budget'     => $data['budget'],
            ':published'  => $data['is_published'] ? 1 : 0,
            ':expired'    => !empty($data['is_expired']) ? 1 : 0,
            ':sort_order' => (int)($data['sort_order'] ?? 0),
            ':updated'    => $now,
        ];

        if (array_key_exists('image', $data)) {
            $sql .= ", image = :image";
            $params[':image'] = $data['image'];
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
