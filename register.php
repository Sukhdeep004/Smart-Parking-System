<?php
session_start();
require_once 'includes/db.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = cleanInput($_POST['fullname']);
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($fullname)) $errors[] = 'Full name is required.';
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters long.';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) $errors[] = 'Username already exists.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = 'Email already registered.';
    }
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $username, $email, $hashed_password])) {
                $_SESSION['register_success'] = 'Registration successful! Please login.';
                header('Location: login.php');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Parking System</title>
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
            padding: 30px 0;
        }
        .register-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        .register-card {
            background: rgba(40,40,40,0.26);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            color: #fff;
            backdrop-filter: blur(6px);
            transition: transform 0.30s cubic-bezier(.32,2,.55,.27), box-shadow 0.20s, background 0.20s;
            animation: slideIn 0.5s ease;
        }
        .register-card:hover {
            transform: translateY(-16px) scale(1.04);
            box-shadow: 0 32px 80px rgba(245,87,108,0.25),
                        0 2px 42px rgba(70, 20, 69, 0.18);
            background: rgba(40,40,70,0.37);
            filter: brightness(1.09) saturate(1.07);
            border: 1.5px solid rgba(245,87,108,0.17);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px);}
            to   { opacity: 1; transform: translateY(0);}
        }
        .register-header, .register-header h2, .register-header p, 
        label, .form-label, .back-link a,
        .input-group-text, .alert, .alert-danger, .alert-success, .mb-0, .text-center, .text-muted {
            color: #fff !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.13);
        }
        input, .form-control {
            background: rgba(255,255,255,0.18) !important;
            color: #fff !important;
            border: 1px solid rgba(238,238,238,0.17);
        }
        ::placeholder { color: #fff8; }
        .form-label { font-weight: 500; }
        .input-group-text {
            background: rgba(255,255,255,0.13);
            color: #fff; border-right: none;
        }
        .form-control {border-left: none;}
        .btn-register {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px #f093fb55;
            color: #fff;
        }
        .back-link { text-align: center; margin-top: 20px;}
        .back-link a { color: #fff; text-decoration: none; font-weight: 500;}
        .alert-danger, .alert-success {
            background: rgba(0,0,0,0.18);
            color: #fff;
            border: none;
        }
        .password-strength {
            height: 5px; border-radius: 3px;
            margin-top: 5px; transition: all 0.3s ease;
            background: #fff1;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="register-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 class="mb-2">Create Account</h2>
            <p class="text-muted">Register to manage parking system</p>
        </div>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-circle"></i> Error!</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $err): ?>
                        <li><?= $err ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="registerForm">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="fullname" placeholder="Enter your full name" 
                    value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Choose a username" 
                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" 
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Create a strong password" required>
                <div class="password-strength" id="strengthBar"></div>
                <small class="text-muted" style="color:#fff9 !important;">Minimum 6 characters</small>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" placeholder="Re-enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-register w-100">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none" style="color: #FFD700">Login here</a></p>
        </div>
    </div>
    <div class="back-link">
        <a href="index.php">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            let color = '', width = '';
            switch(strength) {
                case 0: case 1: color = '#dc3545'; width = '25%'; break;
                case 2: color = '#ffc107'; width = '50%'; break;
                case 3: color = '#17a2b8'; width = '75%'; break;
                case 4: case 5: color = '#28a745'; width = '100%'; break;
            }
            strengthBar.style.backgroundColor = color;
            strengthBar.style.width = width;
        });
    }
</script>
</body>
</html>
