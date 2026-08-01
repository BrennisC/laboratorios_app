CREATE DATABASE vulnshop_db;

\connect vulnshop_db

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(120),
    email VARCHAR(190) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50),
    credit_card VARCHAR(32),
    address TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(180),
    description TEXT,
    price DECIMAL(10,2),
    image TEXT,
    active BOOLEAN DEFAULT true
);

CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    product_id INT,
    user_name VARCHAR(120),
    comment TEXT,
    created_at TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    address TEXT,
    created_at TIMESTAMP
);

CREATE TABLE IF NOT EXISTS flag_submissions (
    id SERIAL PRIMARY KEY,
    flag VARCHAR(255),
    stage VARCHAR(50),
    correct BOOLEAN,
    submitted_at TIMESTAMP
);

ALTER TABLE flag_submissions ADD COLUMN IF NOT EXISTS stage VARCHAR(50);

INSERT INTO users (id, name, email, password, role, credit_card, address) VALUES
    (1, 'Admin User', 'admin@example.com', 'admin123', 'admin', '4111111111111111', 'Admin Street 1'),
    (2, 'Normal User', 'user@example.com', 'user123', 'user', '5555555555554444', 'User Avenue 99')
ON CONFLICT (id) DO NOTHING;

INSERT INTO products (id, name, description, price, image, active) VALUES
    (1, 'Laptop Pro', 'High performance laptop for developers', 1200.00, 'https://picsum.photos/seed/laptop/500/300', true),
    (2, 'Wireless Mouse', 'Ergonomic mouse with long battery life', 30.00, 'https://picsum.photos/seed/mouse/500/300', true),
    (3, 'Mechanical Keyboard', 'RGB keyboard with blue switches', 90.00, 'https://picsum.photos/seed/keyboard/500/300', true),
    (4, 'USB-C Hub', 'Seven-port hub for workstations', 55.00, 'https://picsum.photos/seed/hub/500/300', true),
    (5, 'Smart Watch', 'Fitness tracking watch with insecure profile sync', 150.00, 'https://picsum.photos/seed/watch/500/300', true),
    (6, 'Noise Cancelling Headphones', 'Wireless headphones for remote work', 180.00, 'https://picsum.photos/seed/headphones/500/300', true),
    (7, 'Portable SSD 1TB', 'Fast storage for backups and lab files', 110.00, 'https://picsum.photos/seed/ssd/500/300', true),
    (8, 'Webcam HD', 'USB webcam for meetings and streaming', 45.00, 'https://picsum.photos/seed/webcam/500/300', true)
ON CONFLICT (id) DO NOTHING;

INSERT INTO reviews (id, product_id, user_name, comment, created_at) VALUES
    (1, 1, 'Alice', 'Great laptop for labs.', CURRENT_TIMESTAMP),
    (2, 2, 'Bob', 'Comfortable and cheap.', CURRENT_TIMESTAMP),
    (3, 3, 'Charlie', 'The keyboard feels solid.', CURRENT_TIMESTAMP),
    (4, 5, 'Dana', 'Good device, but the sync feature looks suspicious.', CURRENT_TIMESTAMP)
ON CONFLICT (id) DO NOTHING;

SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE(MAX(id), 1)) FROM users;
SELECT setval(pg_get_serial_sequence('products', 'id'), COALESCE(MAX(id), 1)) FROM products;
SELECT setval(pg_get_serial_sequence('reviews', 'id'), COALESCE(MAX(id), 1)) FROM reviews;

DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'web_app') THEN
        CREATE ROLE web_app LOGIN PASSWORD 'secureshop_pass123';
    END IF;
END
$$;

GRANT ALL PRIVILEGES ON DATABASE vulnshop_db TO web_app;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO web_app;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO web_app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO web_app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO web_app;
