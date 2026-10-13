<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel Root Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

require_admin();
redirect(url('/panel/profile'), 303);
