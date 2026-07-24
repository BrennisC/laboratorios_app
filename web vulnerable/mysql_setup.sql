CREATE DATABASE IF NOT EXISTS vulnshop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vulnshop_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120),
    email VARCHAR(190) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50),
    credit_card VARCHAR(32),
    address TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180),
    description TEXT,
    price DECIMAL(10,2),
    image TEXT,
    active TINYINT DEFAULT 1
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_name VARCHAR(120),
    comment TEXT,
    created_at DATETIME
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    address TEXT,
    created_at DATETIME
);

CREATE TABLE IF NOT EXISTS flag_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flag VARCHAR(255),
    correct TINYINT,
    submitted_at DATETIME
);

INSERT IGNORE INTO users (id, name, email, password, role, credit_card, address) VALUES
    (1, 'Admin User', 'admin@example.com', 'admin123', 'admin', '4111111111111111', 'Admin Street 1'),
    (2, 'Normal User', 'user@example.com', 'user123', 'user', '5555555555554444', 'User Avenue 99');

INSERT IGNORE INTO products (id, name, description, price, image, active) VALUES
    (1, 'Laptop Pro', 'High performance laptop for developers', 1200.00, 'https://picsum.photos/seed/laptop/500/300', 1),
    (2, 'Wireless Mouse', 'Ergonomic mouse with long battery life', 30.00, 'https://picsum.photos/seed/mouse/500/300', 1),
    (3, 'Mechanical Keyboard', 'RGB keyboard with blue switches', 90.00, 'https://picsum.photos/seed/keyboard/500/300', 1),
    (4, 'USB-C Hub', 'Seven-port hub for workstations', 55.00, 'https://picsum.photos/seed/hub/500/300', 1),
    (5, 'Smart Watch', 'Fitness tracking watch with insecure profile sync', 150.00, 'https://picsum.photos/seed/watch/500/300', 1),
    (6, 'Noise Cancelling Headphones', 'Wireless headphones for remote work', 180.00, 'https://picsum.photos/seed/headphones/500/300', 1),
    (7, 'Portable SSD 1TB', 'Fast storage for backups and lab files', 110.00, 'https://picsum.photos/seed/ssd/500/300', 1),
    (8, 'Webcam HD', 'USB webcam for meetings and streaming', 45.00, 'https://picsum.photos/seed/webcam/500/300', 1);

INSERT IGNORE INTO reviews (id, product_id, user_name, comment, created_at) VALUES
    (1, 1, 'Alice', 'Great laptop for labs.', NOW()),
    (2, 2, 'Bob', 'Comfortable and cheap.', NOW()),
    (3, 3, 'Charlie', 'The keyboard feels solid.', NOW()),
    (4, 5, 'Dana', 'Good device, but the sync feature looks suspicious.', NOW());

CREATE USER IF NOT EXISTS 'vulnshop_user'@'localhost' IDENTIFIED BY 'vulnshop_pass123';
CREATE USER IF NOT EXISTS 'vulnshop_user'@'127.0.0.1' IDENTIFIED BY 'vulnshop_pass123';

GRANT ALL PRIVILEGES ON vulnshop_db.* TO 'vulnshop_user'@'localhost';
GRANT ALL PRIVILEGES ON vulnshop_db.* TO 'vulnshop_user'@'127.0.0.1';

FLUSH PRIVILEGES;
