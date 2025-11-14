<?php
require_once 'includes/header.php';

$success = '';
$error = '';
$userId = $user['id'];

// Update profile info (except password)
if (isset($_POST['update_profile'])) {
    $fullname = cleanInput($_POST['fullname']);
    $email = cleanInput($_POST['email']);
    $contact = cleanInput($_POST['contact']);
    try {
        $stmt = $pdo->prepare("UPDATE users SET fullname=?, email=?, contact=? WHERE id=?");
        $stmt->execute([$fullname, $email, $contact, $userId]);
        $success = "Profile updated successfully!";
        // Update current user info for UI
        $user['fullname'] = $fullname;
        $user['email'] = $email;
        $user['contact'] = $contact;
    } catch (PDOException $e) {
        $error = "Failed to update profile. Email might already be taken.";
    }
}

// Change password
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    // Fetch user hash from DB
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$userId]);
    $currentHash = $stmt->fetchColumn();
    if (password_verify($current_password, $currentHash)) {
        if ($new_password !== $confirm_password) {
            $error = "New passwords do not match!";
        } elseif (strlen($new_password) < 6) {
            $error = "Password should be at least 6 characters!";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$new_hash, $userId]);
            $success = "Password updated successfully!";
        }
    } else {
        $error = "Current password is incorrect!";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow fade-in">
                <div class="card-header text-center">
                    <h3 class="mb-0"><i class="fas fa-user-circle"></i> Profile / Edit Your Info</h3>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST" class="mb-4" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact" class="form-control" value="<?= isset($user['contact']) ? htmlspecialchars($user['contact']) : '' ?>" maxlength="15">
                        </div>
                        <button type="submit" name="update_profile" class="btn w-100 mb-2">
                            <i class="fas fa-save"></i> Save Profile
                        </button>
                    </form>
                    <hr>
                    <form method="POST" autocomplete="off">
                        <h5 class="my-3">Change Password</h5>
                        <div class="mb-2">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning w-100">
                            <i class="fas fa-unlock-alt"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
            <div class="card shadow mt-4">
                <div class="card-header text-center">
                    <h5 class="mb-0"><i class="fas fa-id-card"></i> Your Info</h5>
                </div>
                <div class="card-body text-center">
                    <p><b>Name:</b> <?= htmlspecialchars($user['fullname']) ?></p>
                    <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
                    <p><b>Contact:</b> <?= isset($user['contact']) ? htmlspecialchars($user['contact']) : '—' ?></p>
                    <p><b>Username:</b> <?= htmlspecialchars($user['username']) ?></p>
                    <p><b>Last Login:</b> <?= $user['last_login'] ? date('d M Y, h:i A', strtotime($user['last_login'])) : "N/A" ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
