<?php
/**
 * Database Configuration File
 * Car Parking Management System
 */

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'parking_system');

// Timezone Configuration
date_default_timezone_set('Asia/Kolkata');

// Database Connection using PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

/** Get System Setting */
function getSetting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : null;
}

/** Update System Setting */
function updateSetting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

/** Log Activity */
function logActivity($type, $description, $vehicle_number = null, $slot_name = null, $user_id = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO activity_logs (activity_type, description, vehicle_number, slot_name, user_id) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$type, $description, $vehicle_number, $slot_name, $user_id]);
}

/** Check if user is logged in */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/** Get current user data */
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

/** Sanitize Input */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/** Calculate Parking Fee (SETTINGS-AWARE!) */
function calculateFee($entry_time, $exit_time, $vehicle_type = 'car') {
    $entry = strtotime($entry_time);
    $exit = strtotime($exit_time);
    $duration = ceil(($exit - $entry) / 3600); // Hours (rounded up)
    $rate_key = $vehicle_type . '_rate_per_hour';
    $rate = getSetting($rate_key);

    if ($rate === null || !is_numeric($rate) || $rate <= 0)
        $rate = 50; // fallback only if nothing in DB

    $amount = $duration * $rate;

    return [
        'duration' => $duration,
        'rate' => $rate,
        'amount' => $amount
    ];
}
?>
