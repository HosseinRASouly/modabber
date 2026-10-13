<?php
declare(strict_types=1);

/**
 * Modabber Jalali (Shamsi) Date Helper
 *
 * Implements high-precision Gregorian to Jalali calendar algorithm.
 */

/**
 * Converts Gregorian date (year, month, day) to Jalali (year, month, day).
 *
 * @return array{0: int, 1: int, 2: int} [jy, jm, jd]
 */
function gregorian_to_jalali(int $gy, int $gm, int $gd): array
{
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + (int)(($gy2 + 3) / 4) - (int)(($gy2 + 99) / 100) + (int)(($gy2 + 399) / 400) + $gd + $g_d_m[$gm - 1];
    $jy = -1595 + (33 * (int)($days / 12053));
    $days %= 12053;
    $jy += 4 * (int)($days / 1461);
    $days %= 1461;

    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }

    if ($days < 186) {
        $jm = 1 + (int)($days / 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }

    return [$jy, $jm, $jd];
}

/**
 * Formats a timestamp into a Jalali date string with Persian digits.
 * Default format: "Y/m/d H:i"
 */
function jdate(string $format = 'Y/m/d H:i', ?int $timestamp = null): string
{
    $timestamp = $timestamp ?? time();
    $gy = (int)date('Y', $timestamp);
    $gm = (int)date('m', $timestamp);
    $gd = (int)date('d', $timestamp);

    [$jy, $jm, $jd] = gregorian_to_jalali($gy, $gm, $gd);

    $hours = date('H', $timestamp);
    $minutes = date('i', $timestamp);
    $seconds = date('s', $timestamp);

    $replacements = [
        'Y' => sprintf('%04d', $jy),
        'y' => sprintf('%02d', $jy % 100),
        'm' => sprintf('%02d', $jm),
        'n' => (string)$jm,
        'd' => sprintf('%02d', $jd),
        'j' => (string)$jd,
        'H' => $hours,
        'i' => $minutes,
        's' => $seconds,
    ];

    $result = strtr($format, $replacements);
    return fa_digits($result);
}

/**
 * Returns current Jalali Year in Persian digits.
 */
function jalali_year(): string
{
    [$jy] = gregorian_to_jalali((int)date('Y'), (int)date('m'), (int)date('d'));
    return fa_digits((string)$jy);
}
