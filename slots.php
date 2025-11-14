<?php
/**
 * Parking Slot Management
 * Car Parking Management System
 */
require_once 'includes/header.php';

$success = '';
$error = '';

// Handle Add Slot
if (isset($_POST['add_slot'])) {
    $slot_name = cleanInput($_POST['slot_name']);
    $slot_type = cleanInput($_POST['slot_type']);
    $floor_number = (int)$_POST['floor_number'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO parking_slots (slot_name, slot_type, floor_number) VALUES (?, ?, ?)");
        if ($stmt->execute([$slot_name, $slot_type, $floor_number])) {
            $success = "Slot added successfully!";
            logActivity('slot_update', "New slot $slot_name added", null, $slot_name, $user['id']);
        }
    } catch (PDOException $e) {
        $error = "Error: Slot name already exists.";
    }
}

// Handle Delete Slot
if (isset($_GET['delete'])) {
    $slot_id = (int)$_GET['delete'];
    
    // Check if slot is occupied
    $checkStmt = $pdo->prepare("SELECT status, slot_name FROM parking_slots WHERE id = ?");
    $checkStmt->execute([$slot_id]);
    $slot = $checkStmt->fetch();
    
    if ($slot && $slot['status'] == 'Available') {
        $stmt = $pdo->prepare("DELETE FROM parking_slots WHERE id = ?");
        if ($stmt->execute([$slot_id])) {
            $success = "Slot deleted successfully!";
            logActivity('slot_update', "Slot {$slot['slot_name']} deleted", null, $slot['slot_name'], $user['id']);
        }
    } else {
        $error = "Cannot delete occupied slot!";
    }
}

// Fetch all slots
$stmt = $pdo->query("SELECT * FROM parking_slots ORDER BY floor_number, slot_name");
$all_slots = $stmt->fetchAll();

// Group slots by floor
$slots_by_floor = [];
foreach ($all_slots as $slot) {
    $floor = $slot['floor_number'];
    if (!isset($slots_by_floor[$floor])) {
        $slots_by_floor[$floor] = [];
    }
    $slots_by_floor[$floor][] = $slot;
}

// Get statistics
$total_slots = count($all_slots);
$available_slots = count(array_filter($all_slots, function($s) { return $s['status'] == 'Available'; }));
$occupied_slots = count(array_filter($all_slots, function($s) { return $s['status'] == 'Occupied'; }));
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-th-large"></i> Parking Slot Management</h2>
            <p class="text-muted">Manage and monitor parking slots</p>
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

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="text-primary"><?= $total_slots ?></h3>
                    <p class="mb-0">Total Slots</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="text-success"><?= $available_slots ?></h3>
                    <p class="mb-0">Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="text-danger"><?= $occupied_slots ?></h3>
                    <p class="mb-0">Occupied</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Slots Display -->
            <?php foreach ($slots_by_floor as $floor => $slots): ?>
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-building"></i> Floor <?= $floor ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($slots as $slot): ?>
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="slot-card <?= $slot['status'] == 'Available' ? 'available' : 'occupied' ?>">
                                        <div class="slot-header">
                                            <strong><?= htmlspecialchars($slot['slot_name']) ?></strong>
                                            <?php if ($slot['status'] == 'Available'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="deleteSlot(<?= $slot['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="slot-type">
                                            <i class="fas fa-<?= $slot['slot_type'] == 'bike' ? 'motorcycle' : ($slot['slot_type'] == 'truck' ? 'truck' : 'car') ?>"></i>
                                            <?= ucfirst($slot['slot_type']) ?>
                                        </div>
                                        <div class="slot-status">
                                            <?php if ($slot['status'] == 'Available'): ?>
                                                <span class="badge bg-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Occupied</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="col-lg-4">
            <!-- Add New Slot Form -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;" data-aos="fade-left">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Slot</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Slot Name</label>
                            <input type="text" class="form-control" name="slot_name" placeholder="e.g., A1, B2" required>
                            <small class="text-muted">Must be unique</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slot Type</label>
                            <select class="form-select" name="slot_type" required>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                                <option value="truck">Truck</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Floor Number</label>
                            <input type="number" class="form-control" name="floor_number" min="1" value="1" required>
                        </div>

                        <button type="submit" name="add_slot" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Add Slot
                        </button>
                    </form>
                </div>
            </div>

            <!-- Legend -->
            <div class="card border-0 shadow-sm mt-4" data-aos="fade-left" data-aos-delay="100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Legend</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="legend-box bg-success"></div>
                        <span>Available Slot</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="legend-box bg-danger"></div>
                        <span>Occupied Slot</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .slot-card {
        border: 2px solid #dee2e6;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
    }

    .slot-card.available {
        background: #d4edda;
        border-color: #28a745;
    }

    .slot-card.occupied {
        background: #f8d7da;
        border-color: #dc3545;
    }

    .slot-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .slot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .slot-type {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .legend-box {
        width: 30px;
        height: 30px;
        border-radius: 5px;
        margin-right: 10px;
    }
</style>

<script>
function deleteSlot(slotId) {
    if (confirm('Are you sure you want to delete this slot?')) {
        window.location.href = 'slots.php?delete=' + slotId;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>