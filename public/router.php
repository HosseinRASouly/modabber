<?php
declare(strict_types=1);

/**
 * Modabber Router for PHP Built-in Web Server
 *
 * Emulates Apache mod_rewrite rules when running:
 * php -S 0.0.0.0:8000 -t public public/router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$filePath = __DIR__ . $uri;

// 1. Serve static files directly
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    // Prevent direct execution of uploaded scripts or dotfiles
    if (preg_match('/\.(php|phtml|phar|sh|sql|ini|env)$/i', $uri)) {
        http_response_code(403);
        require __DIR__ . '/error.php';
        return;
    }
    return false; // Return false for built-in server to serve static file
}

// 2. Application form POST rewrite: /project/{id}/apply
if (preg_match('#^/project/([0-9]+)/apply/?$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/project-apply.php';
    return;
}

// 3. Project detail rewrite: /project/{id}
if (preg_match('#^/project/([0-9]+)/?$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/project.php';
    return;
}

// 4. Login route
if (preg_match('#^/login/?$#', $uri)) {
    require __DIR__ . '/login.php';
    return;
}

// 5. Panel routes
if (preg_match('#^/panel/?$#', $uri)) {
    require __DIR__ . '/panel/index.php';
    return;
}
if (preg_match('#^/panel/profile/?$#', $uri)) {
    require __DIR__ . '/panel/profile.php';
    return;
}
if (preg_match('#^/panel/projects/?$#', $uri)) {
    require __DIR__ . '/panel/projects.php';
    return;
}
if (preg_match('#^/panel/projects/create/?$#', $uri)) {
    require __DIR__ . '/panel/project-form.php';
    return;
}
if (preg_match('#^/panel/projects/edit/?$#', $uri)) {
    require __DIR__ . '/panel/project-form.php';
    return;
}
if (preg_match('#^/panel/logos/?$#', $uri)) {
    require __DIR__ . '/panel/logos.php';
    return;
}
if (preg_match('#^/panel/logos/create/?$#', $uri)) {
    require __DIR__ . '/panel/logo-form.php';
    return;
}
if (preg_match('#^/panel/logos/edit/?$#', $uri)) {
    require __DIR__ . '/panel/logo-form.php';
    return;
}
if (preg_match('#^/panel/logout/?$#', $uri)) {
    require __DIR__ . '/panel/logout.php';
    return;
}

// 6. Home page
if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    return;
}

// 7. Fallback 404
http_response_code(404);
require __DIR__ . '/error.php';
