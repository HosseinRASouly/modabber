<?php
declare(strict_types=1);

/**
 * Modabber Session Flash Message Helper
 */

function set_flash(string $key, mixed $message): void
{
    session_start_lazy();
    $_SESSION['_flash'][$key] = $message;
}

function get_flash(string $key, mixed $default = null): mixed
{
    session_start_lazy();
    if (!isset($_SESSION['_flash'][$key])) {
        return $default;
    }

    $val = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $val;
}

function has_flash(string $key): bool
{
    session_start_lazy();
    return isset($_SESSION['_flash'][$key]);
}
