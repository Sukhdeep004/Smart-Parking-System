<?php
/**
 * Header Template
 * Included in all authenticated pages
 */
session_start();
require_once 'db.php';

// Check if user is logged in (except for public pages)
$public_pages = ['index.php', 'login.php', 'register.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (!in_array($current_page, $public_pages) && !isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parking Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php if (!in_array($current_page, $public_pages)): ?>
<!-- Navigation Bar for Authenticated Pages -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-parking"></i> Parking System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'slots.php' ? 'active' : '' ?>" href="slots.php">
                        <i class="fas fa-th-large"></i> Slots
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'vehicle_entry.php' ? 'active' : '' ?>" href="vehicle_entry.php">
                        <i class="fas fa-car"></i> Entry
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'vehicle_exit.php' ? 'active' : '' ?>" href="vehicle_exit.php">
                        <i class="fas fa-sign-out-alt"></i> Exit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'reports.php' ? 'active' : '' ?>" href="reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user['fullname']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>