<?php
declare(strict_types=1);

/**
 * Modabber Form Rate Limiter Module
 */

function check_apply_rate_limit(string $ip): array
{
    $repo = new FormSubmissionRepository();

    // Opportunistically cleanup submissions older than 2 days
    if (random_int(1, 25) === 1) {
        $repo->cleanup(172800);
    }

    $limit10Min = (int)app_config('APPLY_RATE_LIMIT_10MIN', 3);
    $limitDay   = (int)app_config('APPLY_RATE_LIMIT_DAY', 10);

    $count10Min = $repo->countByIpInWindow($ip, 600);
    if ($count10Min >= $limit10Min) {
        return [
            'allowed' => false,
            'message' => 'تعداد درخواست‌های شما زیاد است. لطفاً ۱۰ دقیقه دیگر دوباره تلاش کنید.'
        ];
    }

    $countDay = $repo->countByIpInWindow($ip, 86400);
    if ($countDay >= $limitDay) {
        return [
            'allowed' => false,
            'message' => 'سقف مجاز ارسال درخواست روزانه به پایان رسیده است. لطفاً فردا تلاش فرمایید.'
        ];
    }

    return ['allowed' => true];
}
