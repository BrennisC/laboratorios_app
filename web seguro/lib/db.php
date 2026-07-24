<?php
require_once __DIR__ . '/../config.php';

function db()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        ensure_demo_accounts($pdo);
    }

    return $pdo;
}

function ensure_demo_accounts(PDO $pdo)
{
    $accounts = [
        ['Admin User', 'admin@secureshop.local', 'AdminPass123!', 'admin', '1111', 'Admin Street 1'],
        ['Normal User', 'user@secureshop.local', 'UserPass123!', 'user', '4444', 'User Avenue 99'],
    ];

    $select = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
    $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, credit_card_last4, address) VALUES (?, ?, ?, ?, ?, ?)');
    $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ? AND password_hash = ?');

    foreach ($accounts as $account) {
        [$name, $email, $password, $role, $last4, $address] = $account;
        $select->execute([$email]);
        $user = $select->fetch();

        if (!$user) {
            $insert->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $last4, $address]);
            continue;
        }

        if (!password_verify($password, $user['password_hash'])) {
            $update->execute([password_hash($password, PASSWORD_DEFAULT), $email, $user['password_hash']]);
        }
    }
}
