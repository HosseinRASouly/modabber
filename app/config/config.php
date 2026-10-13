<?php
declare(strict_types=1);

/**
 * Modabbernet Application Configuration
 */

return [
    // Brand & General
    'SITE_NAME' => 'مدبرنت | اتصال هوشمند صنعت به متخصصان',
    'SHORT_NAME' => 'مدبرنت',
    'BASE_URL'  => getenv('BASE_URL') ?: '',
    'APP_ENV'   => getenv('APP_ENV') ?: 'development',
    'APP_DEBUG' => (bool)(getenv('APP_DEBUG') ?: true),
    'FORCE_HTTPS' => (bool)(getenv('FORCE_HTTPS') ?: false),
    'TRUSTED_PROXY' => (bool)(getenv('TRUSTED_PROXY') ?: false),
    'CURRENCY_LABEL' => 'تومان',

    // Database (MySQL / MariaDB with SQLite local fallback support)
    'DB_DRIVER'  => getenv('DB_DRIVER') ?: 'sqlite',
    'DB_HOST'    => getenv('DB_HOST') ?: 'localhost',
    'DB_PORT'    => (int)(getenv('DB_PORT') ?: 3306),
    'DB_NAME'    => getenv('DB_NAME') ?: 'modabber_db',
    'DB_USER'    => getenv('DB_USER') ?: 'modabber_user',
    'DB_PASS'    => getenv('DB_PASS') ?: 'password_placeholder',
    'DB_CHARSET' => 'utf8mb4',

    // Mail Configuration
    'MAIL_DRIVER'     => getenv('MAIL_DRIVER') ?: 'mail',
    'MAIL_HOST'       => getenv('MAIL_HOST') ?: 'smtp.example.com',
    'MAIL_PORT'       => (int)(getenv('MAIL_PORT') ?: 587),
    'MAIL_ENCRYPTION' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'MAIL_USERNAME'   => getenv('MAIL_USERNAME') ?: 'no-reply@modabbernet.ir',
    'MAIL_PASSWORD'   => getenv('MAIL_PASSWORD') ?: 'smtp_password_placeholder',
    'MAIL_VERIFY_TLS' => true,
    'MAIL_FROM_ADDRESS' => getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@modabbernet.ir',
    'MAIL_FROM_NAME'    => 'مدبرنت',
    'MAIL_TO_ADDRESS'   => getenv('MAIL_TO_ADDRESS') ?: 'info@modabbernet.ir',

    // Application Form Limits
    'APPLY_MAX_PDF_BYTES'     => 5 * 1024 * 1024,
    'APPLY_RATE_LIMIT_10MIN'  => 3,
    'APPLY_RATE_LIMIT_DAY'    => 10,
    'APPLY_MIN_FILL_SECONDS'  => 3,
];
