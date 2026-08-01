<?php
require_once __DIR__ . '/config.php';

function db()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        seed_database($pdo);
    }

    return $pdo;
}

function seed_database(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(120),
        email VARCHAR(190) UNIQUE,
        password VARCHAR(255),
        role VARCHAR(50),
        credit_card VARCHAR(32),
        address TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS products (
        id SERIAL PRIMARY KEY,
        category_id INT,
        name VARCHAR(180),
        description TEXT,
        price DECIMAL(10,2),
        image TEXT,
        active BOOLEAN DEFAULT true
    )');

    $pdo->exec('ALTER TABLE products ADD COLUMN IF NOT EXISTS category_id INT');

    $pdo->exec('CREATE TABLE IF NOT EXISTS reviews (
        id SERIAL PRIMARY KEY,
        product_id INT,
        user_name VARCHAR(120),
        comment TEXT,
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS orders (
        id SERIAL PRIMARY KEY,
        user_id INT,
        total DECIMAL(10,2),
        address TEXT,
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS categories (
        id SERIAL PRIMARY KEY,
        name VARCHAR(120),
        description TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS contacts (
        id SERIAL PRIMARY KEY,
        name VARCHAR(120),
        email VARCHAR(190),
        message TEXT,
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS user_history (
        id SERIAL PRIMARY KEY,
        user_id INT,
        action TEXT,
        ip_address VARCHAR(80),
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS reports (
        id SERIAL PRIMARY KEY,
        title VARCHAR(180),
        content TEXT,
        created_by INT,
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS password_resets (
        id SERIAL PRIMARY KEY,
        email VARCHAR(190),
        token VARCHAR(255),
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS api_keys (
        id SERIAL PRIMARY KEY,
        user_id INT,
        api_key VARCHAR(255),
        scope VARCHAR(120),
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS user_sessions (
        id SERIAL PRIMARY KEY,
        user_id INT,
        session_token VARCHAR(255),
        ip_address VARCHAR(80),
        user_agent TEXT,
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS api_events (
        id SERIAL PRIMARY KEY,
        endpoint VARCHAR(180),
        method VARCHAR(20),
        payload TEXT,
        ip_address VARCHAR(80),
        created_at TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS flag_submissions (
        id SERIAL PRIMARY KEY,
        flag VARCHAR(255),
        stage VARCHAR(50),
        correct BOOLEAN,
        submitted_at TIMESTAMP
    )');

    $pdo->exec('ALTER TABLE flag_submissions ADD COLUMN IF NOT EXISTS stage VARCHAR(50)');

    if (!is_dir(__DIR__ . '/data')) {
        @mkdir(__DIR__ . '/data', 0777, true);
    }

    ensure_lab_flags();

    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $reviewCount = (int) $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
    $categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    $historyCount = (int) $pdo->query('SELECT COUNT(*) FROM user_history')->fetchColumn();
    $reportCount = (int) $pdo->query('SELECT COUNT(*) FROM reports')->fetchColumn();
    $apiKeyCount = (int) $pdo->query('SELECT COUNT(*) FROM api_keys')->fetchColumn();

    if ($userCount === 0) {
        $pdo->exec("INSERT INTO users (name, email, password, role, credit_card, address) VALUES
            ('Admin User', 'admin@example.com', 'admin123', 'admin', '4111111111111111', 'Admin Street 1'),
            ('Normal User', 'user@example.com', 'user123', 'user', '5555555555554444', 'User Avenue 99')");
    }

    if ($productCount === 0) {
        $pdo->exec("INSERT INTO products (name, description, price, image, active) VALUES
            ('Laptop Pro', 'High performance laptop for developers', 1200, 'https://picsum.photos/seed/laptop/500/300', true),
            ('Wireless Mouse', 'Ergonomic mouse with long battery life', 30, 'https://picsum.photos/seed/mouse/500/300', true),
            ('Mechanical Keyboard', 'RGB keyboard with blue switches', 90, 'https://picsum.photos/seed/keyboard/500/300', true),
            ('USB-C Hub', 'Seven-port hub for workstations', 55, 'https://picsum.photos/seed/hub/500/300', true),
            ('Smart Watch', 'Fitness tracking watch with insecure profile sync', 150, 'https://picsum.photos/seed/watch/500/300', true),
            ('Noise Cancelling Headphones', 'Wireless headphones for remote work', 180, 'https://picsum.photos/seed/headphones/500/300', true),
            ('Portable SSD 1TB', 'Fast storage for backups and lab files', 110, 'https://picsum.photos/seed/ssd/500/300', true),
            ('Webcam HD', 'USB webcam for meetings and streaming', 45, 'https://picsum.photos/seed/webcam/500/300', true)");
    }

    if ($reviewCount === 0) {
        $pdo->exec("INSERT INTO reviews (product_id, user_name, comment, created_at) VALUES
            (1, 'Alice', 'Great laptop for labs.', CURRENT_TIMESTAMP),
            (2, 'Bob', 'Comfortable and cheap.', CURRENT_TIMESTAMP),
            (3, 'Charlie', 'The keyboard feels solid.', CURRENT_TIMESTAMP),
            (5, 'Dana', 'Good device, but the sync feature looks suspicious.', CURRENT_TIMESTAMP)");
    }

    if ($categoryCount === 0) {
        $pdo->exec("INSERT INTO categories (name, description) VALUES
            ('Computers', 'Laptops and workstations'),
            ('Accessories', 'Mice, keyboards and adapters'),
            ('Storage', 'Backup and portable storage devices')");
    }

    if ($historyCount === 0) {
        $pdo->exec("INSERT INTO user_history (user_id, action, ip_address, created_at) VALUES
            (1, 'Admin exported product report', '127.0.0.1', CURRENT_TIMESTAMP),
            (2, 'User updated shipping address', '127.0.0.1', CURRENT_TIMESTAMP),
            (2, 'User viewed order history', '127.0.0.1', CURRENT_TIMESTAMP)");
    }

    if ($reportCount === 0) {
        $pdo->exec("INSERT INTO reports (title, content, created_by, created_at) VALUES
            ('Monthly Sales Draft', 'Pending review by back office.', 1, CURRENT_TIMESTAMP),
            ('Inventory Sync Notes', 'Some internal service checks are failing intermittently.', 1, CURRENT_TIMESTAMP)");
    }

    if ($apiKeyCount === 0) {
        $pdo->exec("INSERT INTO api_keys (user_id, api_key, scope, created_at) VALUES
            (1, 'adminkey-legacy-123', 'admin:*', CURRENT_TIMESTAMP),
            (2, 'userkey-public-456', 'user:read', CURRENT_TIMESTAMP)");
    }
}

function challenge_flags()
{
    return [
        'recon' => lab_flag('recon'),
        'user' => lab_flag('user'),
        'admin' => lab_flag('admin'),
        'root' => lab_flag('root'),
        'rce' => lab_flag('rce'),
    ];
}

function ensure_lab_flags()
{
    foreach (['recon', 'user', 'admin', 'root', 'rce'] as $stage) {
        lab_flag($stage);
    }

    $rootPath = __DIR__ . '/data/root.txt';
    $legacyRootPath = __DIR__ . '/data/root_flag.txt';
    if (!file_exists($legacyRootPath) && file_exists($rootPath)) {
        @copy($rootPath, $legacyRootPath);
    }

    $rcePath = __DIR__ . '/data/rce.txt';
    $legacyRcePath = __DIR__ . '/data/rce_flag.txt';
    if (!file_exists($legacyRcePath) && file_exists($rcePath)) {
        @copy($rcePath, $legacyRcePath);
    }
}

function lab_flag($stage)
{
    $paths = [
        'recon' => __DIR__ . '/data/recon.txt',
        'user' => __DIR__ . '/data/user.txt',
        'admin' => __DIR__ . '/data/admin.txt',
        'root' => __DIR__ . '/data/root.txt',
        'rce' => __DIR__ . '/data/rce.txt',
    ];

    if (!isset($paths[$stage])) {
        return '';
    }

    if (!is_dir(__DIR__ . '/data')) {
        @mkdir(__DIR__ . '/data', 0777, true);
    }

    if (!file_exists($paths[$stage])) {
        $uuid = lab_uuid();
        @file_put_contents($paths[$stage], 'VSHOP{' . $stage . '_' . $uuid . '}' . "\n");
    }

    return trim((string) @file_get_contents($paths[$stage]));
}

function lab_uuid()
{
    $hex = bin2hex(random_bytes(16));
    return sprintf('%s-%s-%s-%s-%s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20));
}

function flag_stage($flag)
{
    foreach (challenge_flags() as $stage => $expected) {
        if (hash_equals($expected, $flag)) {
            return $stage;
        }
    }

    return null;
}

function solved_stages()
{
    $solved = [];
    $rows = db()->query('SELECT DISTINCT stage FROM flag_submissions WHERE correct = true AND stage IS NOT NULL')->fetchAll(PDO::FETCH_COLUMN);

    foreach ($rows as $stage) {
        $solved[$stage] = true;
    }

    return $solved;
}
