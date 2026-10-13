-- Modabber Database Schema
-- UTF-8 (utf8mb4) & InnoDB for MySQL / MariaDB

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL UNIQUE,
    email VARCHAR(190) NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(32) NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    short_description TEXT NOT NULL,
    full_description MEDIUMTEXT NOT NULL,
    budget BIGINT UNSIGNED NOT NULL,
    image VARCHAR(64) NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_projects_list (is_published, sort_order, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS company_logos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    image VARCHAR(64) NOT NULL,
    website_url VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_logos_active (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    identifier VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_login_ip (ip_address, created_at),
    INDEX idx_login_ident (identifier, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS form_submissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    project_id INT UNSIGNED NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    INDEX idx_submissions_ip (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Settings Seeding (No Admin user here; admin is created via scripts/create_admin.php)
INSERT INTO settings (setting_key, setting_value, updated_at) VALUES
('hero_title', 'کسب‌وکار خود را به سطح بعدی ببرید.', NOW()),
('hero_description', 'رشد کسب‌وکار کوچک شما را با مدبر بهبود می‌دهیم. همچنین به ارتقای خدمات مشتریان و کسب درآمد سریع‌تر کمک می‌کنیم.', NOW()),
('logos_caption', 'مورد اعتماد شرکت‌ها و سازمان‌ها', NOW()),
('why_eyebrow', 'درباره مدبر', NOW()),
('why_title', 'چرا مدبر؟', NOW()),
('why_description', 'ما پاسخ‌های مناسب نیازهای شما را ارائه می‌دهیم و رضایت مشتری را بالاتر از هر چیز می‌دانیم.', NOW()),
('feature_1_title', 'مدیریت آسان', NOW()),
('feature_1_text', 'متن نمونه برای توضیح کوتاه این ویژگی که بعداً جایگزین می‌شود.', NOW()),
('feature_2_title', 'امنیت بالا', NOW()),
('feature_2_text', 'متن نمونه برای توضیح کوتاه این ویژگی که بعداً جایگزین می‌شود.', NOW()),
('feature_3_title', 'رابط کاربری تمیز', NOW()),
('feature_3_text', 'متن نمونه برای توضیح کوتاه این ویژگی که بعداً جایگزین می‌شود.', NOW()),
('stat_1_value', '۱۲۰+', NOW()),
('stat_1_label', 'پروژه انجام‌شده', NOW()),
('stat_2_value', '۵۰+', NOW()),
('stat_2_label', 'مشتری راضی', NOW()),
('stat_3_value', '۱۰+', NOW()),
('stat_3_label', 'سال تجربه', NOW()),
('projects_subtitle', 'نمونه‌ای از پروژه‌هایی که با افتخار انجام داده‌ایم.', NOW()),
('apply_title', 'ارسال درخواست همکاری', NOW()),
('apply_subtitle', 'برای همکاری در این پروژه، مشخصات و رزومه خود را برای ما ارسال کنید.', NOW()),
('company_phone', '۰۲۱-۱۲۳۴۵۶۷۸', NOW()),
('company_email', 'info@example.com', NOW()),
('company_address', 'تهران، خیابان نمونه، پلاک ۱۰', NOW()),
('social_telegram', 'https://t.me/username', NOW()),
('social_instagram', 'https://instagram.com/username', NOW()),
('social_whatsapp', '', NOW()),
('social_linkedin', '', NOW())
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
