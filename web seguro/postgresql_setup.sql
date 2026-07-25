CREATE DATABASE secureshop_db;

\connect secureshop_db

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'user_role') THEN
        CREATE TYPE user_role AS ENUM ('user', 'admin');
    END IF;
END
$$;

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role user_role NOT NULL DEFAULT 'user',
    credit_card_last4 CHAR(4),
    address TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image TEXT NOT NULL,
    active BOOLEAN NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id SERIAL PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    success BOOLEAN NOT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS security_logs (
    id SERIAL PRIMARY KEY,
    user_id INT NULL,
    event_type VARCHAR(80) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

INSERT INTO users (id, name, email, password_hash, role, credit_card_last4, address) VALUES
    (1, 'Admin User', 'admin@secureshop.local', 'SET_BY_PHP_PASSWORD_HASH', 'admin', '1111', 'Admin Street 1'),
    (2, 'Normal User', 'user@secureshop.local', 'SET_BY_PHP_PASSWORD_HASH', 'user', '4444', 'User Avenue 99')
ON CONFLICT (id) DO NOTHING;

INSERT INTO products (id, name, description, price, image, active) VALUES
    (1, 'Laptop Pro', 'High performance laptop for developers', 1200.00, 'https://picsum.photos/seed/secure-laptop/500/300', true),
    (2, 'Wireless Mouse', 'Ergonomic mouse with long battery life', 30.00, 'https://picsum.photos/seed/secure-mouse/500/300', true),
    (3, 'Mechanical Keyboard', 'RGB keyboard with blue switches', 90.00, 'https://picsum.photos/seed/secure-keyboard/500/300', true),
    (4, 'USB-C Hub', 'Seven-port hub for workstations', 55.00, 'https://picsum.photos/seed/secure-hub/500/300', true),
    (5, 'Smart Watch', 'Fitness tracking watch with privacy controls', 150.00, 'https://picsum.photos/seed/secure-watch/500/300', true),
    (6, 'Noise Cancelling Headphones', 'Wireless headphones for remote work', 180.00, 'https://picsum.photos/seed/secure-headphones/500/300', true),
    (7, 'Portable SSD 1TB', 'Fast encrypted-ready external storage', 110.00, 'https://picsum.photos/seed/secure-ssd/500/300', true),
    (8, 'Webcam HD', 'USB webcam for meetings and streaming', 45.00, 'https://picsum.photos/seed/secure-webcam/500/300', true)
ON CONFLICT (id) DO NOTHING;

SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE(MAX(id), 1)) FROM users;
SELECT setval(pg_get_serial_sequence('products', 'id'), COALESCE(MAX(id), 1)) FROM products;

DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'secureshop_user') THEN
        CREATE ROLE secureshop_user LOGIN PASSWORD 'secureshop_pass123';
    END IF;
END
$$;

GRANT CONNECT ON DATABASE secureshop_db TO secureshop_user;
GRANT USAGE ON SCHEMA public TO secureshop_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO secureshop_user;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO secureshop_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO secureshop_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT USAGE, SELECT ON SEQUENCES TO secureshop_user;
