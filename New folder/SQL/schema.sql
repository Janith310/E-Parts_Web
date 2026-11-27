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

CREATE TABLE part_fitment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    part_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    fitment_notes VARCHAR(150) DEFAULT 'Direct OEM Fit',
    FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_fitment (part_id, vehicle_id)
) ENGINE=InnoDB;

INSERT INTO vehicles(make, model, chassis_code, engine_code, year_from, year_to) VALUES
('Nissan', 'Sunny', 'FB15', 'QG15DE', 1998, 2004),
('Nissan', 'Sunny', 'SB13', 'CD17', 1990, 1994),
('Nissan', 'Caravan', 'VRGE24', 'TD27', 1986, 2001),
('Daihatsu', 'Rugger', 'F75', 'DL2.8', 1984, 1993),
('Toyota', 'Hiace', 'LH50', 'L', 1983, 1988);

INSERT INTO parts(part_number, part_name, category, brand, price, stock_qty) VALUES
('15208-65F0A', 'Spin-On Engine Oil Filter', 'Filters', 'Nissan Genuine', 2450.00, 18),
('41060-4M490', 'Front Disc Brake Pad Set', 'Brakes', 'Akebono', 8900.00, 7),
('54500-4M400', 'Front Lower Control Arm (RH)', 'Suspension', '555 Japan', 14500.00, 4),
('16546-V0100', 'Air Filter Element', 'Filters', 'Sakura', 2100.00, 25),
('23300-2J201', 'Starter Motor Assembly 12V', 'Electrical', 'Denso', 32000.00, 2),
('17801-87604', 'Heavy Duty Air Cleaner Element', 'Filters', 'VIC Japan', 3600.00, 9);

INSERT INTO part_fitment (part_id, vehicle_id, fitment_notes) VALUES
(1, 1, 'Fits QG13DE / QG15DE / QG18DE'),
(1, 5, 'Cross-compatible thread M20x1.5'),
(2, 1, '2WD Sedan models only'),
(3, 1, 'Includes ball joint & bushings'),
(4, 1, 'Standard rectangular box'),
(4, 3, 'Fits E24 petrol/diesel air box'),
(5, 3, 'TD27 / TD25 9-Tooth Pinion'),
(6, 4, 'Fits F70 / F75 2.8L Diesel');