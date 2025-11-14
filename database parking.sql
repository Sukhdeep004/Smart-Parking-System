-- =============================================
-- Car Parking Management System Database
-- Complete MySQL Schema with Sample Data
-- =============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS parking_system;
USE parking_system;

-- =============================================
-- Table 1: Users (Admin Accounts)
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table 2: Parking Slots
-- =============================================
CREATE TABLE parking_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_name VARCHAR(10) UNIQUE NOT NULL,
    slot_type ENUM('car', 'bike', 'truck') DEFAULT 'car',
    status ENUM('Available', 'Occupied') DEFAULT 'Available',
    floor_number INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table 3: Vehicles
-- =============================================
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_name VARCHAR(255) NOT NULL,
    vehicle_number VARCHAR(20) UNIQUE NOT NULL,
    vehicle_type ENUM('car', 'bike', 'truck') DEFAULT 'car',
    contact VARCHAR(20) NOT NULL,
    slot_id INT NULL,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME NULL,
    amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Parked', 'Exited') DEFAULT 'Parked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (slot_id) REFERENCES parking_slots(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table 4: Transactions (Payment History)
-- =============================================
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    slot_name VARCHAR(10) NOT NULL,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME NOT NULL,
    duration_hours DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('paid', 'pending') DEFAULT 'paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table 5: System Settings
-- =============================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value VARCHAR(255) NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table 6: Activity Logs
-- =============================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_type ENUM('entry', 'exit', 'payment', 'slot_update') NOT NULL,
    description TEXT NOT NULL,
    vehicle_number VARCHAR(20) NULL,
    slot_name VARCHAR(10) NULL,
    user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Sample Data: Users
-- Password: admin123 (hashed with bcrypt)
-- =============================================
INSERT INTO users (fullname, username, email, password, role) VALUES
('Admin User', 'admin', 'admin@parking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin'),
('John Manager', 'john', 'john@parking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =============================================
-- Sample Data: Parking Slots (50 slots)
-- =============================================
INSERT INTO parking_slots (slot_name, slot_type, status, floor_number) VALUES
-- Floor 1 - Cars
('A1', 'car', 'Available', 1), ('A2', 'car', 'Occupied', 1), ('A3', 'car', 'Available', 1),
('A4', 'car', 'Available', 1), ('A5', 'car', 'Occupied', 1), ('A6', 'car', 'Available', 1),
('A7', 'car', 'Available', 1), ('A8', 'car', 'Available', 1), ('A9', 'car', 'Occupied', 1),
('A10', 'car', 'Available', 1),
-- Floor 2 - Cars
('B1', 'car', 'Available', 2), ('B2', 'car', 'Available', 2), ('B3', 'car', 'Occupied', 2),
('B4', 'car', 'Available', 2), ('B5', 'car', 'Available', 2), ('B6', 'car', 'Available', 2),
('B7', 'car', 'Occupied', 2), ('B8', 'car', 'Available', 2), ('B9', 'car', 'Available', 2),
('B10', 'car', 'Available', 2),
-- Bike Slots
('M1', 'bike', 'Available', 1), ('M2', 'bike', 'Occupied', 1), ('M3', 'bike', 'Available', 1),
('M4', 'bike', 'Available', 1), ('M5', 'bike', 'Available', 1),
-- Truck Slots
('T1', 'truck', 'Available', 1), ('T2', 'truck', 'Available', 1), ('T3', 'truck', 'Occupied', 1);

-- =============================================
-- Sample Data: Vehicles (Currently Parked)
-- =============================================
INSERT INTO vehicles (owner_name, vehicle_number, vehicle_type, contact, slot_id, entry_time, status) VALUES
('Rajesh Kumar', 'MH12AB1234', 'car', '9876543210', 2, '2025-10-30 10:30:00', 'Parked'),
('Priya Sharma', 'DL3CAB5678', 'car', '9876543211', 5, '2025-10-30 11:15:00', 'Parked'),
('Amit Patel', 'GJ01CD9012', 'car', '9876543212', 9, '2025-10-30 12:00:00', 'Parked'),
('Sneha Desai', 'KA05EF3456', 'car', '9876543213', 13, '2025-10-30 13:30:00', 'Parked'),
('Vikram Singh', 'UP16GH7890', 'car', '9876543214', 17, '2025-10-30 14:00:00', 'Parked'),
('Anjali Reddy', 'TN09IJ2345', 'bike', '9876543215', 22, '2025-10-30 09:00:00', 'Parked'),
('Rohan Gupta', 'MH14KL6789', 'truck', '9876543216', 28, '2025-10-30 08:00:00', 'Parked');

-- =============================================
-- Sample Data: Transactions (Past Records)
-- =============================================
INSERT INTO transactions (vehicle_id, vehicle_number, owner_name, slot_name, entry_time, exit_time, duration_hours, amount) VALUES
(1, 'MH04XY1111', 'Suresh Iyer', 'A1', '2025-10-29 09:00:00', '2025-10-29 12:00:00', 3.00, 150.00),
(2, 'DL8YZ2222', 'Meera Joshi', 'A3', '2025-10-29 10:00:00', '2025-10-29 15:30:00', 5.50, 275.00),
(3, 'GJ06AB3333', 'Karan Mehta', 'B1', '2025-10-29 11:00:00', '2025-10-29 14:00:00', 3.00, 150.00),
(4, 'KA09CD4444', 'Divya Nair', 'A7', '2025-10-28 08:00:00', '2025-10-28 18:00:00', 10.00, 500.00),
(5, 'UP20EF5555', 'Rahul Verma', 'B5', '2025-10-28 12:00:00', '2025-10-28 17:00:00', 5.00, 250.00),
(6, 'TN12GH6666', 'Lakshmi Bhat', 'M1', '2025-10-27 07:00:00', '2025-10-27 19:00:00', 12.00, 300.00),
(7, 'MH16IJ7777', 'Arjun Shah', 'T1', '2025-10-27 06:00:00', '2025-10-27 20:00:00', 14.00, 1050.00),
(8, 'DL5KL8888', 'Pooja Kapoor', 'A2', '2025-10-26 10:00:00', '2025-10-26 13:00:00', 3.00, 150.00);

-- =============================================
-- Sample Data: System Settings
-- =============================================
INSERT INTO settings (setting_key, setting_value, description) VALUES
('car_rate_per_hour', '50', 'Parking rate for cars per hour (in INR)'),
('bike_rate_per_hour', '25', 'Parking rate for bikes per hour (in INR)'),
('truck_rate_per_hour', '75', 'Parking rate for trucks per hour (in INR)'),
('currency', 'INR', 'System currency'),
('timezone', 'Asia/Kolkata', 'System timezone'),
('company_name', 'Smart Parking Solutions', 'Company/Organization name'),
('contact_email', 'support@parking.com', 'Support contact email'),
('contact_phone', '+91-9876543210', 'Support contact phone');

-- =============================================
-- Sample Data: Activity Logs
-- =============================================
INSERT INTO activity_logs (activity_type, description, vehicle_number, slot_name, user_id) VALUES
('entry', 'Vehicle entered parking area', 'MH12AB1234', 'A2', 1),
('entry', 'Vehicle entered parking area', 'DL3CAB5678', 'A5', 1),
('exit', 'Vehicle exited, payment completed', 'MH04XY1111', 'A1', 1),
('exit', 'Vehicle exited, payment completed', 'DL8YZ2222', 'A3', 1),
('slot_update', 'Slot status updated to Available', NULL, 'A1', 1);

-- =============================================
-- Indexes for Performance
-- =============================================
CREATE INDEX idx_vehicle_status ON vehicles(status);
CREATE INDEX idx_slot_status ON parking_slots(status);
CREATE INDEX idx_transaction_date ON transactions(created_at);
CREATE INDEX idx_activity_date ON activity_logs(created_at);

-- =============================================
-- Views for Quick Reports
-- =============================================

-- View: Current Parking Summary
CREATE VIEW v_parking_summary AS
SELECT 
    COUNT(*) as total_slots,
    SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_slots,
    SUM(CASE WHEN status = 'Occupied' THEN 1 ELSE 0 END) as occupied_slots
FROM parking_slots;

-- View: Today's Revenue
CREATE VIEW v_today_revenue AS
SELECT 
    COUNT(*) as total_transactions,
    SUM(amount) as total_revenue,
    AVG(duration_hours) as avg_duration
FROM transactions
WHERE DATE(created_at) = CURDATE();

-- View: Currently Parked Vehicles
CREATE VIEW v_parked_vehicles AS
SELECT 
    v.id, v.owner_name, v.vehicle_number, v.vehicle_type, v.contact,
    ps.slot_name, v.entry_time,
    TIMESTAMPDIFF(HOUR, v.entry_time, NOW()) as hours_parked
FROM vehicles v
JOIN parking_slots ps ON v.slot_id = ps.id
WHERE v.status = 'Parked'
ORDER BY v.entry_time DESC;

-- =============================================
-- End of Database Schema
-- =============================================