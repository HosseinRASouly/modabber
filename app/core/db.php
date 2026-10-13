<?php
declare(strict_types=1);

/**
 * Modabber Database Layer (PDO)
 *
 * Supports MySQL/MariaDB for production/cPanel, with automatic SQLite fallback
 * for local preview environments.
 */

class Database
{
    private static ?\PDO $pdo = null;

    public static function getConnection(): \PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $driver = app_config('DB_DRIVER', 'sqlite');

        if ($driver === 'mysql') {
            try {
                $host    = app_config('DB_HOST', '127.0.0.1');
                $port    = app_config('DB_PORT', 3306);
                $dbname  = app_config('DB_NAME', 'modabber_db');
                $user    = app_config('DB_USER', 'root');
                $pass    = app_config('DB_PASS', '');
                $charset = app_config('DB_CHARSET', 'utf8mb4');

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                $options = [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => false,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci",
                ];

                self::$pdo = new \PDO($dsn, $user, $pass, $options);
                return self::$pdo;
            } catch (\PDOException $e) {
                app_log("MySQL connection failed: " . $e->getMessage(), 'error');
                // If in dev and mysql fails, fall back to SQLite
                if (app_config('APP_ENV') !== 'production') {
                    $driver = 'sqlite';
                } else {
                    throw $e;
                }
            }
        }

        // SQLite Driver (Zero-config local preview)
        $dbPath = APP_PATH . '/storage/database.sqlite';
        $isNew = !file_exists($dbPath);
        $dsn = "sqlite:" . $dbPath;

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        self::$pdo = new \PDO($dsn, null, null, $options);

        if ($isNew) {
            self::initSqliteTables(self::$pdo);
        }

        return self::$pdo;
    }

    private static function initSqliteTables(\PDO $pdo): void
    {
        $schemaSql = <<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NULL UNIQUE,
            full_name TEXT NOT NULL,
            phone TEXT NULL,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            is_active INTEGER NOT NULL DEFAULT 1,
            last_login_at TEXT NULL,
            created_at TEXT NOT NULL,
            updated_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            short_description TEXT NOT NULL,
            full_description TEXT NOT NULL,
            budget INTEGER NOT NULL,
            image TEXT NULL,
            is_published INTEGER NOT NULL DEFAULT 1,
            is_expired INTEGER NOT NULL DEFAULT 0,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL,
            updated_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS company_logos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            image TEXT NOT NULL,
            website_url TEXT NULL,
            is_active INTEGER NOT NULL DEFAULT 1,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL,
            updated_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT NULL,
            updated_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            identifier TEXT NOT NULL,
            created_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS form_submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            project_id INTEGER NOT NULL,
            status TEXT NOT NULL DEFAULT 'pending',
            created_at TEXT NOT NULL
        );
SQL;
        $pdo->exec($schemaSql);

        try {
            $pdo->exec("ALTER TABLE projects ADD COLUMN is_expired INTEGER NOT NULL DEFAULT 0");
        } catch (\Throwable $e) {
            // Already added
        }

        // Seed default settings
        $now = date('Y-m-d H:i:s');
        $defaults = [
            'hero_title' => 'مدبرنت؛ اتصال هوشمند صنعت به متخصصان',
            'hero_description' => 'ما عارضه‌ها را دقیق می‌شناسیم، تیم‌ها را با دقت ارزیابی می‌کنیم و مطمئن‌ترین متخصصان را برای اجرای پروژه‌ها معرفی می‌کنیم.',
            'logos_caption' => 'مورد اعتماد شرکت‌ها و سازمان‌ها',
            'why_eyebrow' => '',
            'why_title' => 'چرا مدبرنت؟',
            'why_description' => 'ما فاصله میان چالش‌های صنعتی و راهکارهای تخصصی را با اعتبار، مهندسی و نظارت دقیق پر می‌کنیم.',
            'feature_1_title' => 'عارضه‌یابی تخصصی',
            'feature_1_text' => 'استخراج و تعریف استاندارد نیازهای واقعی صنایع با همراهی نخبگان دانشگاهی و متخصصان مهندسی صنایع',
            'feature_2_title' => 'امنیت و شفافیت قراردادها',
            'feature_2_text' => 'صیانت از بودجه کارفرما، ضابطه‌مند کردن توافق‌ها و نظارت بر حسن انجام کار تا تحویل نهایی پروژه',
            'feature_3_title' => 'ارزیابی هوشمند متخصصان',
            'feature_3_text' => 'سنجش دقیق اهلیت و توان فنی تیم‌های اجرا پیش از معرفی به صنعت برای دستیابی به بالاترین کیفیت اجرای پروژه',
            'stat_1_value' => '۱۲۰+',
            'stat_1_label' => 'پروژه انجام‌شده',
            'stat_2_value' => '۵۰+',
            'stat_2_label' => 'مشتری راضی',
            'stat_3_value' => '۱۰+',
            'stat_3_label' => 'سال تجربه',
            'projects_subtitle' => 'نمونه‌ای از پروژه‌هایی که با افتخار انجام داده‌ایم.',
            'apply_title' => 'ارسال درخواست همکاری',
            'apply_subtitle' => 'برای همکاری در این پروژه، مشخصات و رزومه خود را برای ما ارسال کنید.',
            'company_phone' => '۰۲۱-۱۲۳۴۵۶۷۸',
            'company_email' => 'info@example.com',
            'company_address' => 'تهران، خیابان نمونه، پلاک ۱۰',
            'social_telegram' => 'https://t.me/username',
            'social_instagram' => 'https://instagram.com/username',
            'social_whatsapp' => '',
            'social_linkedin' => '',
        ];

        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value, updated_at) VALUES (:k, :v, :u)");
        foreach ($defaults as $k => $v) {
            $stmt->execute([':k' => $k, ':v' => $v, ':u' => $now]);
        }

        // Seed 3 sample projects
        $projStmt = $pdo->prepare("INSERT INTO projects (name, short_description, full_description, budget, image, is_published, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $projStmt->execute([
            'طراحی سامانه یکپارچه مالی و انبارداری',
            'پیاده‌سازی پلتفرم جامع ابری با قابلیت گزارش‌گیری پیشرفته و رهگیری لحظه‌ای موجودی کالا.',
            "پروژه پیاده‌سازی سامانه یکپارچه مالی، حسابداری و انبارداری سازمانی با استانداردهای مدرن امنیت و کارایی. این سیستم توانایی مدیریت چندین انبار مجزا، ثبت فاکتورهای رسمی، صدور اسناد حسابداری خودکار و تهیه صورت‌های مالی استاندارد را فراهم می‌سازد.\n\nویژگی‌های فنی سامانه:\n- رابط کاربری بهینه‌سازی‌شده برای زبان فارسی و پشتیبانی از تقویم جلالی\n- سیستم احراز هویت دومرحله‌ای و کنترل دسترسی بر اساس نقش (RBAC)\n- پشتیبانی از حجم بالای تراکنش‌ها با پاسخ‌دهی زیر ۱۰۰ میلی‌ثانیه\n- سیستم پشتیبان‌گیری منظم و رمزنگاری داده‌های حساس\n\nهم‌اکنون تیم توسعه مدبر در حال گسترش ماژول‌های تحلیلی هوش تجاری و ادغام با وب‌سرویس‌های بانکی است.",
            45000000,
            null,
            1,
            1,
            $now,
            $now
        ]);
        $projStmt->execute([
            'توسعه پورتال خدمات مشتریان و پشتیبانی آنلاین',
            'سامانه تعاملی تیکتینگ و پایگاه دانش سازمانی با طراحی مدرن و واکنش‌گرا.',
            "پورتال جامع امور مشتریان به منظور بهبود سرعت پاسخ‌گویی و افزایش رضایت مخاطبان طراحی گردیده است. کاربران در این سامانه قادر خواهند بود درخواست‌های پشتیبانی خود را ارسال، وضعیت تیکت‌ها را پیگیری و به بانک مقالات آموزشی دسترسی داشته باشند.\n\nبخش‌های اصلی پروژه:\n- سیستم تیکتینگ چندسطحی با قابلیت ارجاع هوشمند بین کارشناسان\n- پایگاه دانش تعاملی با جستجوی سریع فارسی\n- داشبورد آماری نرخ پاسخ‌گویی و شاخص رضایت مشتریان (CSAT)\n- اعلان‌های پیامکی و ایمیلی لحظه‌ای\n\nاین پروژه با تکیه بر معماری مقیاس‌پذیر و رابط کاربری مینیمال اجرا شده است.",
            28000000,
            null,
            1,
            2,
            $now,
            $now
        ]);
        $projStmt->execute([
            'سامانه هوشمند پایش و بهینه‌سازی فرآیندها',
            'اتوماسیون گردش کار اداری و مدیریت هوشمند وظایف تیم‌های اجرایی سازمان.',
            "سامانه جریان کاری مدبر بستری پیشرفته برای تبدیل فرآیندهای سنتی کاغذی به فرآیندهای چابک دیجیتال است. با استفاده از موتور فرم‌ساز داینامیک و قوانین شرطی گردش کار، جریان تاییدات اداری با نهایت دقت و سرعت انجام می‌پذیرد.\n\nمزایای کلیدی سامانه:\n- تعریف فرآیندهای اختصاصی بدون نیاز به برنامه‌نویسی مجدد\n- گزارش‌گیری زمان سپری‌شده در هر مرحله از فرآیند\n- نسخه موبایل بهینه‌سازی شده با دسترسی آفلاین مقدماتی\n- انطباق کامل با پروتکل‌های امنیتی و حریم خصوصی\n\nپروژه با موفقیت به بهره‌برداری رسیده و فاز توسعه امکانات تکمیلی در جریان است.",
            62000000,
            null,
            1,
            3,
            $now,
            $now
        ]);

        // Seed sample company logos
        $logoStmt = $pdo->prepare("INSERT INTO company_logos (name, image, website_url, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $logos = [
            ['فناوران ارتباطات نوین', 'logo1.svg', 'https://example.com', 1, 1],
            ['گروه سرمایه‌گذاری سپهر', 'logo2.svg', 'https://example.com', 1, 2],
            ['سامانه‌های داده‌پرداز پارس', 'logo3.svg', 'https://example.com', 1, 3],
            ['هلدینگ توسعه کسب‌وکار مهر', 'logo4.svg', 'https://example.com', 1, 4],
            ['صنایع پیشرو هوشمند', 'logo5.svg', 'https://example.com', 1, 5],
            ['توسعه راهبردی پایدار', 'logo6.svg', 'https://example.com', 1, 6],
        ];
        foreach ($logos as $l) {
            $logoStmt->execute([$l[0], $l[1], $l[2], $l[3], $l[4], $now, $now]);
        }
    }
}
