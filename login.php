<?php
session_start();
require_once 'includes/db.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['fullname'];
                $_SESSION['admin_role'] = $user['role'];
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Login error. Please try again.';
        }
    }
}
if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Parking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('./assets/img/img4.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .login-card {
            background: rgba(40,40,40,0.28);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            color: #fff;
            backdrop-filter: blur(6px);
            transition: transform 0.30s cubic-bezier(.32,2,.55,.27), box-shadow 0.20s, background 0.20s;
            animation: slideIn 0.5s ease;
        }
        .login-card:hover {
            transform: translateY(-16px) scale(1.04);
            box-shadow: 0 32px 80px rgba(13, 110, 253, 0.29),
                        0 2px 42px rgba(20, 21, 69, 0.16);
            background: rgba(40,40,70,0.43);
            filter: brightness(1.08) saturate(1.1);
            border: 1.5px solid rgba(102,126,234,0.13);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px);}
            to   { opacity: 1; transform: translateY(0);}
        }
        .login-header, .login-header h2, .login-header p, 
        label, .form-label, .back-home a,
        .input-group-text, .alert, .alert-danger, .alert-success, .mb-0, .text-center, .text-muted {
            color: #fff !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.11);
        }
        input, .form-control {
            background: rgba(255,255,255,0.18) !important;
            color: #fff !important;
            border: 1px solid rgba(238,238,238,0.19);
        }
        ::placeholder { color: #fff8; }
        .input-group-text {
            background: rgba(255,255,255,0.13);
            color: #fff; border-right: none;
        }
        .form-control {border-left: none;}
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #fff;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .back-home { text-align: center; margin-top: 20px; }
        .back-home a { color: #fff; text-decoration: none; font-weight: 500; }
        .btn-outline-secondary, .btn-outline-secondary:focus {
            color:#fff;
            border-color:#807eea;
            background: rgba(120,120,140,0.13);
        }
        .btn-outline-secondary:hover {
            background: #667eea;
            color:#fff;
        }
        .alert-danger, .alert-success {
            background: rgba(0,0,0,0.18);
            color: #fff;
            border: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-parking"></i>
            </div>
            <h2 class="mb-2">Admin Login</h2>
            <p class="text-muted">Welcome back! Please login to your account.</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control" name="username" placeholder="Enter username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none" style="color:#FFD700">Register here</a></p>
        </div>
    </div>
    <div class="back-home">
        <a href="index.php">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>
