DROP DATABASE IF EXISTS db_gymhub;
CREATE DATABASE db_gymhub;
USE db_gymhub;

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    join_date DATE NOT NULL
);

CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL
);

CREATE TABLE memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    package_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_paid DECIMAL(12,2) NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (package_id) REFERENCES packages(id)
);

INSERT INTO members (name, phone, email, join_date) VALUES
('Asep Kuncoro', '081234567890', 'asepkun@email.com', '2026-08-01'),
('Owi Solo', '083256647271', 'jackowi@email.com', '2025-09-10'),
('Megawati Lestari', '087855448978', 'megachan@email.com', '2026-10-05');

INSERT INTO packages (package_name, price, duration_days) VALUES
('Daily', 30000, 1),
('Monthly', 300000, 30),
('Yearly', 2800000, 365);