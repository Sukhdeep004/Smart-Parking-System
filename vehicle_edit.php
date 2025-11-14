<?php
require_once 'includes/header.php';

// Get vehicle ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid vehicle!');
}
$vehicle_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    die("Vehicle not found!");
}

$success = '';
$error = '';
if (isset($_POST['update_vehicle'])) {
    $vehicle_number = cleanInput($_POST['vehicle_number']);
    $owner_name = cleanInput($_POST['owner_name']);
    $vehicle_type = cleanInput($_POST['vehicle_type']);
    // Add more fields if needed

    $stmt = $pdo->prepare("UPDATE vehicles SET vehicle_number=?, owner_name=?, vehicle_type=? WHERE id=?");
    if ($stmt->execute([$vehicle_number, $owner_name, $vehicle_type, $vehicle_id])) {
        $success = "Vehicle details updated successfully!";
        // Correct reload: fresh SELECT query
        $stmt2 = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
        $stmt2->execute([$vehicle_id]);
        $vehicle = $stmt2->fetch();
    } else {
        $error = "Failed to update vehicle.";
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Vehicle</h5>
                </div>
                <div class="card-body">
                    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control"
                                value="<?= htmlspecialchars($vehicle['vehicle_number']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner Name</label>
                            <input type="text" name="owner_name" class="form-control"
                                value="<?= htmlspecialchars($vehicle['owner_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select">
                                <option <?= $vehicle['vehicle_type']=="car" ? "selected":""; ?> value="car">Car</option>
                                <option <?= $vehicle['vehicle_type']=="bike" ? "selected":""; ?> value="bike">Bike</option>
                                <option <?= $vehicle['vehicle_type']=="truck" ? "selected":""; ?> value="truck">Truck</option>
                            </select>
                        </div>
                        <!-- Add more fields as needed -->
                        <button type="submit" name="update_vehicle" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Update Vehicle
                        </button>
                    </form>
                    <hr>
                    <a href="vehicle_entry.php" class="btn btn-secondary w-100">Back to Parking</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
