CREATE DATABASE IF NOT EXISTS spare_parts_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spare_parts_db;

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    chassis_code VARCHAR(30) NOT NULL,
    engine_code VARCHAR(30) DEFAULT NULL,
    year_from SMALLINT UNSIGNED NOT NULL,
    year_to SMALLINT UNSIGNED NOT NULL,
    INDEX idx_chassis (chassis_code),
    INDEX idx_make_model (make, model)
) ENGINE=InnoDB;

CREATE TABLE parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    part_number VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL,
    category ENUM('Engine', 'Brakes', 'Suspension', 'Electrical', 'Filters', 'Cooling', 'Drivetrain') NOT NULL,
    brand VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_qty INT NOT NULL DEFAULT 0,
    INDEX idx_part_num (part_number),
    INDEX idx_category (category)
) ENGINE=InnoDB;