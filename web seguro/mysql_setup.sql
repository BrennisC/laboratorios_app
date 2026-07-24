CREATE DATABASE IF NOT EXISTS secureshop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE secureshop_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    credit_card_last4 CHAR(4),
    address TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image TEXT NOT NULL,
    active TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    success TINYINT NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    event_type VARCHAR(80) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL
);

INSERT IGNORE INTO users (id, name, email, password_hash, role, credit_card_last4, address) VALUES
    (1, 'Admin User', 'admin@secureshop.local', 'SET_BY_PHP_PASSWORD_HASH', 'admin', '1111', 'Admin Street 1'),
    (2, 'Normal User', 'user@secureshop.local', 'SET_BY_PHP_PASSWORD_HASH', 'user', '4444', 'User Avenue 99');

INSERT IGNORE INTO products (id, name, description, price, image, active) VALUES
    (1, 'Laptop Pro', 'High performance laptop for developers', 1200.00, 'https://picsum.photos/seed/secure-laptop/500/300', 1),
    (2, 'Wireless Mouse', 'Ergonomic mouse with long battery life', 30.00, 'https://picsum.photos/seed/secure-mouse/500/300', 1),
    (3, 'Mechanical Keyboard', 'RGB keyboard with blue switches', 90.00, 'https://picsum.photos/seed/secure-keyboard/500/300', 1),
    (4, 'USB-C Hub', 'Seven-port hub for workstations', 55.00, 'https://picsum.photos/seed/secure-hub/500/300', 1),
    (5, 'Smart Watch', 'Fitness tracking watch with privacy controls', 150.00, 'https://picsum.photos/seed/secure-watch/500/300', 1),
    (6, 'Noise Cancelling Headphones', 'Wireless headphones for remote work', 180.00, 'https://picsum.photos/seed/secure-headphones/500/300', 1),
    (7, 'Portable SSD 1TB', 'Fast encrypted-ready external storage', 110.00, 'https://picsum.photos/seed/secure-ssd/500/300', 1),
    (8, 'Webcam HD', 'USB webcam for meetings and streaming', 45.00, 'https://picsum.photos/seed/secure-webcam/500/300', 1);

CREATE USER IF NOT EXISTS 'secureshop_user'@'localhost' IDENTIFIED BY 'secureshop_pass123';
CREATE USER IF NOT EXISTS 'secureshop_user'@'127.0.0.1' IDENTIFIED BY 'secureshop_pass123';
GRANT SELECT, INSERT, UPDATE, DELETE ON secureshop_db.* TO 'secureshop_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON secureshop_db.* TO 'secureshop_user'@'127.0.0.1';
FLUSH PRIVILEGES;
