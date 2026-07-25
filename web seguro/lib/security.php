<?php
require_once __DIR__ . '/../config.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        log_security_event('csrf_failed', 'Invalid CSRF token');
        http_response_code(403);
        exit('Forbidden');
    }
}

function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

function current_user_role()
{
    return $_SESSION['role'] ?? 'guest';
}

function is_logged_in()
{
    return current_user_id() !== null;
}

function require_login()
{
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_admin()
{
    require_login();
    if (current_user_role() !== 'admin') {
        log_security_event('access_denied', 'Non-admin tried to access admin page');
        http_response_code(403);
        exit('Forbidden');
    }
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

function int_param($name, $default = 0)
{
    return filter_input(INPUT_GET, $name, FILTER_VALIDATE_INT) ?: $default;
}

function post_int($name, $default = 0)
{
    return filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT) ?: $default;
}

function log_security_event($type, $message)
{
    try {
        require_once __DIR__ . '/db.php';
        $stmt = db()->prepare('INSERT INTO security_logs (user_id, event_type, message, ip_address, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)');
        $stmt->execute([current_user_id(), $type, $message, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    } catch (Throwable $e) {
        error_log('Security log failed: ' . $e->getMessage());
    }
}

function mask_card($card)
{
    $digits = preg_replace('/\D+/', '', (string) $card);
    return str_repeat('*', max(0, strlen($digits) - 4)) . substr($digits, -4);
}
