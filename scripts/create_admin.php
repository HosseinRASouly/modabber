<?php
declare(strict_types=1);

/**
 * Modabber CLI Script - Create Admin User
 *
 * Usage: php scripts/create_admin.php
 */

if (PHP_SAPI !== 'cli') {
    header('HTTP/1.1 403 Forbidden');
    echo "This script must only be run via the CLI.\n";
    exit(1);
}

require_once __DIR__ . '/../app/core/bootstrap.php';

echo "==========================================\n";
echo "   " . app_config('SITE_NAME', 'مدبر') . " - ایجاد مدیر اولیه سامانه\n";
echo "==========================================\n\n";

$repo = new UserRepository();

// 1. Username
$username = '';
while (empty($username)) {
    echo "نام کاربری (حداقل ۳ حرف انگلیسی): ";
    $input = trim((string)fgets(STDIN));
    if (preg_match('/^[a-zA-Z0-9_\-\.]{3,32}$/', $input)) {
        if ($repo->findByUsernameOrEmail($input)) {
            echo "خطا: این نام کاربری قبلاً ثبت شده است.\n";
        } else {
            $username = $input;
        }
    } else {
        echo "خطا: نام کاربری نامعتبر است (تنها حروف، ارقام و کاراکترهای _ - . مجاز است).\n";
    }
}

// 2. Full Name
$fullName = '';
while (empty($fullName)) {
    echo "نام و نام خانوادگی: ";
    $input = trim((string)fgets(STDIN));
    if (mb_strlen($input, 'UTF-8') >= 2) {
        $fullName = $input;
    } else {
        echo "خطا: نام و نام خانوادگی حداقل باید ۲ حرف باشد.\n";
    }
}

// 3. Email (Optional)
$email = null;
echo "پست الکترونیکی (اختیاری، جهت ادامه Enter بزنید): ";
$inputEmail = trim((string)fgets(STDIN));
if (!empty($inputEmail)) {
    if (filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
        if ($repo->findByUsernameOrEmail($inputEmail)) {
            echo "هشدار: این ایمیل قبلاً ثبت شده؛ ادامه بدون ایمیل صورت می‌گیرد.\n";
        } else {
            $email = $inputEmail;
        }
    } else {
        echo "ایمیل نامعتبر نادیده گرفته شد.\n";
    }
}

// 4. Password (Entered twice, minimum 10 chars)
$password = '';
while (empty($password)) {
    echo "رمز عبور (حداقل ۱۰ نویسه): ";
    // Read password
    $pass1 = '';
    if (stripos(PHP_OS, 'WIN') === 0) {
        $pass1 = trim((string)fgets(STDIN));
    } else {
        // Hide terminal echo on Linux / macOS
        $pass1 = trim((string)shell_exec('stty -echo; head -n 1; stty echo'));
        echo "\n";
    }

    if (strlen($pass1) < 10) {
        echo "خطا: طول رمز عبور باید حداقل ۱۰ نویسه باشد.\n";
        continue;
    }

    echo "تکرار رمز عبور: ";
    $pass2 = '';
    if (stripos(PHP_OS, 'WIN') === 0) {
        $pass2 = trim((string)fgets(STDIN));
    } else {
        $pass2 = trim((string)shell_exec('stty -echo; head -n 1; stty echo'));
        echo "\n";
    }

    if ($pass1 !== $pass2) {
        echo "خطا: تکرار رمز عبور مطابقت ندارد. لطفاً دوباره تلاش کنید.\n";
        continue;
    }

    $password = $pass1;
}

// Hash password
$algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
$options = ($algo === PASSWORD_BCRYPT) ? ['cost' => 12] : [];
$passwordHash = password_hash($password, $algo, $options);

try {
    $userId = $repo->createAdmin($username, $fullName, $email, $passwordHash);
    echo "\n------------------------------------------\n";
    echo "کاربر مدیر با شناسه [{$userId}] و نام کاربری «{$username}» با موفقیت ساخته شد!\n";
    echo "آدرس ورود: " . url('/login') . "\n";
    echo "------------------------------------------\n";
} catch (\Throwable $e) {
    echo "خطا در ایجاد مدیر: " . $e->getMessage() . "\n";
    exit(1);
}
