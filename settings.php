<?php
/**
 * System Settings
 * Car Parking Management System
 */
require_once 'includes/header.php';

$success = '';
$error = '';

// Handle settings update
if (isset($_POST['update_settings'])) {
    try {
        // Prevent empty or zero rates for safety
        $car_rate   = max(1, intval($_POST['car_rate']));
        $bike_rate  = max(1, intval($_POST['bike_rate']));
        $truck_rate = max(1, intval($_POST['truck_rate']));
        updateSetting('car_rate_per_hour', $car_rate);
        updateSetting('bike_rate_per_hour', $bike_rate);
        updateSetting('truck_rate_per_hour', $truck_rate);

        updateSetting('company_name', cleanInput($_POST['company_name']));
        updateSetting('contact_email', cleanInput($_POST['contact_email']));
        updateSetting('contact_phone', cleanInput($_POST['contact_phone']));
        $success = "Settings updated successfully!";
    } catch (Exception $e) {
        $error = "Error updating settings.";
    }
}

// ... [password change and UI stays as before] ...

//<!-- Paste your last settings.php HTML form here (no change needed) -->

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                
                if ($stmt->execute([$hashed, $user['id']])) {
                    $success = "Password changed successfully!";
                } else {
                    $error = "Error changing password.";
                }
            } else {
                $error = "New password must be at least 6 characters.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-cog"></i> System Settings</h2>
            <p class="text-muted">Configure system preferences and rates</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6">
            <!-- Parking Rates -->
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-right">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Parking Rates</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Car Rate (₹ per hour)</label>
                            <input type="number" class="form-control" name="car_rate" 
                                   value="<?= getSetting('car_rate_per_hour') ?>" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bike Rate (₹ per hour)</label>
                            <input type="number" class="form-control" name="bike_rate" 
                                   value="<?= getSetting('bike_rate_per_hour') ?>" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Truck Rate (₹ per hour)</label>
                            <input type="number" class="form-control" name="truck_rate" 
                                   value="<?= getSetting('truck_rate_per_hour') ?>" min="1" required>
                        </div>

                        <hr>

                        <h6 class="mb-3">Company Information</h6>

                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" 
                                   value="<?= getSetting('company_name') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" 
                                   value="<?= getSetting('contact_email') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" 
                                   value="<?= getSetting('contact_phone') ?>" required>
                        </div>

                        <button type="submit" name="update_settings" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Change Password -->
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-left">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-key"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="passwordForm">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" 
                                   id="new_password" minlength="6" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" 
                                   id="confirm_password" required>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-lock"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- User Profile -->
            <div class="card border-0 shadow-sm" data-aos="fade-left" data-aos-delay="100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Full Name:</th>
                            <td><?= htmlspecialchars($user['fullname']) ?></td>
                        </tr>
                        <tr>
                            <th>Username:</th>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td><span class="badge bg-primary"><?= ucfirst($user['role']) ?></span></td>
                        </tr>
                        <tr>
                            <th>Last Login:</th>
                            <td><?= $user['last_login'] ? date('d M Y, h:i A', strtotime($user['last_login'])) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Account Created:</th>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="row mt-4">
        <div class="col-12" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> System Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <?php
                        $total_users = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
                        $total_vehicles = $pdo->query("SELECT COUNT(*) as count FROM vehicles")->fetch()['count'];
                        $total_transactions = $pdo->query("SELECT COUNT(*) as count FROM transactions")->fetch()['count'];
                        $lifetime_revenue = $pdo->query("SELECT COALESCE(SUM(amount), 0) as revenue FROM transactions")->fetch()['revenue'];
                        ?>
                        
                        <div class="col-md-3">
                            <h3 class="text-primary"><?= $total_users ?></h3>
                            <p class="text-muted">Total Admin Users</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success"><?= $total_vehicles ?></h3>
                            <p class="text-muted">Total Vehicle Records</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-info"><?= $total_transactions ?></h3>
                            <p class="text-muted">Total Transactions</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-warning">₹<?= number_format($lifetime_revenue, 2) ?></h3>
                            <p class="text-muted">Lifetime Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New passwords do not match!');
        return false;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
