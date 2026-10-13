<?php
declare(strict_types=1);

/**
 * Modabber Company Logo Repository
 */

class LogoRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getActive(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM company_logos WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
        );
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM company_logos ORDER BY sort_order ASC, id ASC"
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM company_logos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare(
            "INSERT INTO company_logos (name, image, website_url, is_active, sort_order, created_at, updated_at)
             VALUES (:name, :image, :url, :active, :sort_order, :created, :updated)"
        );

        $stmt->execute([
            ':name'       => $data['name'],
            ':image'      => $data['image'],
            ':url'        => !empty($data['website_url']) ? $data['website_url'] : null,
            ':active'     => $data['is_active'] ? 1 : 0,
            ':sort_order' => (int)($data['sort_order'] ?? 0),
            ':created'    => $now,
            ':updated'    => $now,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE company_logos SET name = :name, website_url = :url,
                is_active = :active, sort_order = :sort_order, updated_at = :updated";

        $params = [
            ':id'         => $id,
            ':name'       => $data['name'],
            ':url'        => !empty($data['website_url']) ? $data['website_url'] : null,
            ':active'     => $data['is_active'] ? 1 : 0,
            ':sort_order' => (int)($data['sort_order'] ?? 0),
            ':updated'    => $now,
        ];

        if (array_key_exists('image', $data) && !empty($data['image'])) {
            $sql .= ", image = :image";
            $params[':image'] = $data['image'];
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM company_logos");
        return (int)$stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM company_logos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
