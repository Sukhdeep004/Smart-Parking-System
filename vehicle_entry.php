<?php
/**
 * Vehicle Entry System
 * Car Parking Management System
 */
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_name = cleanInput($_POST['owner_name']);
    $vehicle_number = strtoupper(cleanInput($_POST['vehicle_number']));
    $vehicle_type = cleanInput($_POST['vehicle_type']);
    $contact = cleanInput($_POST['contact']);
    
    // Check if vehicle already parked
    $checkStmt = $pdo->prepare("SELECT id FROM vehicles WHERE vehicle_number = ? AND status = 'Parked'");
    $checkStmt->execute([$vehicle_number]);
    
    if ($checkStmt->fetch()) {
        $error = "This vehicle is already parked in the system!";
    } else {
        // Find available slot of matching type
        $slotStmt = $pdo->prepare("SELECT id, slot_name FROM parking_slots WHERE status = 'Available' AND slot_type = ? ORDER BY slot_name LIMIT 1");
        $slotStmt->execute([$vehicle_type]);
        $available_slot = $slotStmt->fetch();
        
        if ($available_slot) {
            try {
                $pdo->beginTransaction();
                
                // Insert vehicle record
                $insertStmt = $pdo->prepare("INSERT INTO vehicles (owner_name, vehicle_number, vehicle_type, contact, slot_id, entry_time) VALUES (?, ?, ?, ?, ?, NOW())");
                $insertStmt->execute([$owner_name, $vehicle_number, $vehicle_type, $contact, $available_slot['id']]);
                
                // Update slot status
                $updateStmt = $pdo->prepare("UPDATE parking_slots SET status = 'Occupied' WHERE id = ?");
                $updateStmt->execute([$available_slot['id']]);
                
                // Log activity
                logActivity('entry', "Vehicle $vehicle_number entered and assigned to {$available_slot['slot_name']}", $vehicle_number, $available_slot['slot_name'], $user['id']);
                
                $pdo->commit();
                
                $success = "Vehicle entry recorded successfully! Assigned to slot: <strong>{$available_slot['slot_name']}</strong>";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error recording entry. Please try again.";
            }
        } else {
            $error = "No available slots for this vehicle type!";
        }
    }
}

// Get available slots count by type
$carSlots = $pdo->query("SELECT COUNT(*) as count FROM parking_slots WHERE status = 'Available' AND slot_type = 'car'")->fetch()['count'];
$bikeSlots = $pdo->query("SELECT COUNT(*) as count FROM parking_slots WHERE status = 'Available' AND slot_type = 'bike'")->fetch()['count'];
$truckSlots = $pdo->query("SELECT COUNT(*) as count FROM parking_slots WHERE status = 'Available' AND slot_type = 'truck'")->fetch()['count'];

// Recent entries
$recentStmt = $pdo->query("SELECT v.*, ps.slot_name FROM vehicles v 
                           JOIN parking_slots ps ON v.slot_id = ps.id 
                           WHERE v.status = 'Parked' 
                           ORDER BY v.entry_time DESC 
                           LIMIT 10");
$recent_entries = $recentStmt->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-car"></i> Vehicle Entry</h2>
            <p class="text-muted">Record new vehicle arrivals</p>
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

    <!-- Available Slots Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-car text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?= $carSlots ?></h3>
                    <p class="mb-0 text-muted">Car Slots Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-motorcycle text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?= $bikeSlots ?></h3>
                    <p class="mb-0 text-muted">Bike Slots Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-truck text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?= $truckSlots ?></h3>
                    <p class="mb-0 text-muted">Truck Slots Available</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <!-- Entry Form -->
            <div class="card border-0 shadow-sm" data-aos="fade-right">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> New Vehicle Entry</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="entryForm">
                        <div class="mb-3">
                            <label class="form-label">Owner Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="owner_name" placeholder="Enter owner's full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="vehicle_number" 
                                   placeholder="e.g., MH12AB1234" pattern="[A-Za-z0-9]+" required>
                            <small class="text-muted">Format: MH12AB1234</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="vehicle_type" required>
                                <option value="">-- Select Type --</option>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                                <option value="truck">Truck</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="contact" 
                                   placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Entry time will be recorded automatically
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check"></i> Record Entry
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <!-- Recent Entries -->
            <div class="card border-0 shadow-sm" data-aos="fade-left">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Entries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vehicle No.</th>
                                    <th>Owner</th>
                                    <th>Type</th>
                                    <th>Slot</th>
                                    <th>Entry Time</th>
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_entries)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No recent entries</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_entries as $entry): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($entry['vehicle_number']) ?></strong></td>
                                            <td><?= htmlspecialchars($entry['owner_name']) ?></td>
                                            <td>
                                                <i class="fas fa-<?= $entry['vehicle_type'] == 'bike' ? 'motorcycle' : ($entry['vehicle_type'] == 'truck' ? 'truck' : 'car') ?>"></i>
                                                <?= ucfirst($entry['vehicle_type']) ?>
                                            </td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($entry['slot_name']) ?></span></td>
                                            <td><?= date('d M, h:i A', strtotime($entry['entry_time'])) ?></td>
                                            <td><?= htmlspecialchars($entry['contact']) ?></td>
                                            <td>
                                            <a href="vehicle_edit.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mt-4" data-aos="fade-left" data-aos-delay="100">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary"><?= count($recent_entries) ?></h4>
                            <p class="mb-0 text-muted small">Currently Parked</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success"><?= $carSlots + $bikeSlots + $truckSlots ?></h4>
                            <p class="mb-0 text-muted small">Available Slots</p>
                        </div>
                        <div class="col-4">
                            <?php
                            $todayEntries = $pdo->query("SELECT COUNT(*) as count FROM vehicles WHERE DATE(entry_time) = CURDATE()")->fetch()['count'];
                            ?>
                            <h4 class="text-info"><?= $todayEntries ?></h4>
                            <p class="mb-0 text-muted small">Today's Entries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-uppercase vehicle number
document.querySelector('input[name="vehicle_number"]').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});

// Form validation
document.getElementById('entryForm').addEventListener('submit', function(e) {
    const vehicleNumber = document.querySelector('input[name="vehicle_number"]').value;
    const contact = document.querySelector('input[name="contact"]').value;
    
    if (vehicleNumber.length < 6) {
        e.preventDefault();
        alert('Vehicle number must be at least 6 characters long');
        return false;
    }
    
    if (contact.length !== 10) {
        e.preventDefault();
        alert('Contact number must be exactly 10 digits');
        return false;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>