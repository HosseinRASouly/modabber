<?php
declare(strict_types=1);

/**
 * Modabber Setting Repository
 */

class SettingRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function get(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1");
        $stmt->execute([':k' => $key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null) ? (string)$val : $default;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
        return $settings;
    }

    public function set(string $key, string $value): void
    {
        $now = date('Y-m-d H:i:s');
        $driver = app_config('DB_DRIVER', 'sqlite');

        if ($driver === 'mysql') {
            $sql = "INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (:k, :v, :u)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)";
        } else {
            $sql = "INSERT OR REPLACE INTO settings (setting_key, setting_value, updated_at) VALUES (:k, :v, :u)";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':k' => $key, ':v' => $value, ':u' => $now]);
    }
}
