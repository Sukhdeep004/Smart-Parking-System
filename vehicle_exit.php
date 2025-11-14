<?php
/**
 * Vehicle Exit & Fee Calculation
 * Car Parking Management System
 */
require_once 'includes/header.php';

$success = '';
$error = '';

// Handle vehicle exit
if (isset($_POST['process_exit'])) {
    $vehicle_id = (int)$_POST['vehicle_id'];
    
    // Get vehicle details
    $stmt = $pdo->prepare("SELECT v.*, ps.slot_name FROM vehicles v 
                           JOIN parking_slots ps ON v.slot_id = ps.id 
                           WHERE v.id = ? AND v.status = 'Parked'");
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch();
    
    if ($vehicle) {
        $exit_time = date('Y-m-d H:i:s');
        $fee_data = calculateFee($vehicle['entry_time'], $exit_time, $vehicle['vehicle_type']);
        
        try {
            $pdo->beginTransaction();
            
            // Update vehicle record
            $updateVehicle = $pdo->prepare("UPDATE vehicles SET exit_time = ?, amount = ?, status = 'Exited' WHERE id = ?");
            $updateVehicle->execute([$exit_time, $fee_data['amount'], $vehicle_id]);
            
            // Free up the slot
            $updateSlot = $pdo->prepare("UPDATE parking_slots SET status = 'Available' WHERE id = ?");
            $updateSlot->execute([$vehicle['slot_id']]);
            
            // Record transaction
            $insertTransaction = $pdo->prepare("INSERT INTO transactions (vehicle_id, vehicle_number, owner_name, slot_name, entry_time, exit_time, duration_hours, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insertTransaction->execute([
                $vehicle_id,
                $vehicle['vehicle_number'],
                $vehicle['owner_name'],
                $vehicle['slot_name'],
                $vehicle['entry_time'],
                $exit_time,
                $fee_data['duration'],
                $fee_data['amount']
            ]);
            
            // Log activity
            logActivity('exit', "Vehicle {$vehicle['vehicle_number']} exited from {$vehicle['slot_name']}, Amount: ₹{$fee_data['amount']}", $vehicle['vehicle_number'], $vehicle['slot_name'], $user['id']);
            
            $pdo->commit();
            
            $success = "Exit processed successfully! Amount due: <strong>₹" . number_format($fee_data['amount'], 2) . "</strong>";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error processing exit. Please try again.";
        }
    } else {
        $error = "Vehicle not found or already exited!";
    }
}

// Get all currently parked vehicles
$stmt = $pdo->query("SELECT v.*, ps.slot_name, 
                     TIMESTAMPDIFF(HOUR, v.entry_time, NOW()) as hours_parked 
                     FROM vehicles v 
                     JOIN parking_slots ps ON v.slot_id = ps.id 
                     WHERE v.status = 'Parked' 
                     ORDER BY v.entry_time");
$parked_vehicles = $stmt->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-sign-out-alt"></i> Vehicle Exit & Payment</h2>
            <p class="text-muted">Process vehicle exits and calculate fees</p>
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

    <!-- Parked Vehicles List -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Currently Parked Vehicles (<?= count($parked_vehicles) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($parked_vehicles)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">No vehicles currently parked</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vehicle No.</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Slot</th>
                                <th>Entry Time</th>
                                <th>Duration</th>
                                <th>Estimated Fee</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parked_vehicles as $vehicle): 
                                $current_fee = calculateFee($vehicle['entry_time'], date('Y-m-d H:i:s'), $vehicle['vehicle_type']);
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($vehicle['vehicle_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($vehicle['owner_name']) ?></td>
                                    <td>
                                        <i class="fas fa-<?= $vehicle['vehicle_type'] == 'bike' ? 'motorcycle' : ($vehicle['vehicle_type'] == 'truck' ? 'truck' : 'car') ?>"></i>
                                        <?= ucfirst($vehicle['vehicle_type']) ?>
                                    </td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($vehicle['slot_name']) ?></span></td>
                                    <td><?= date('d M, h:i A', strtotime($vehicle['entry_time'])) ?></td>
                                    <td><?= $vehicle['hours_parked'] ?> hrs</td>
                                    <td><strong class="text-success">₹<?= number_format($current_fee['amount'], 2) ?></strong></td>
                                    <td><?= htmlspecialchars($vehicle['contact']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="processExit(<?= $vehicle['id'] ?>, '<?= htmlspecialchars($vehicle['vehicle_number']) ?>', <?= $current_fee['amount'] ?>)">
                                            <i class="fas fa-sign-out-alt"></i> Exit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<!-- Rate Card -->
<div class="row mt-4">
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-car"></i> Car Rate</h6>
            </div>
            <div class="card-body text-center">
                <h3 class="text-primary">₹<?= htmlspecialchars(getSetting('car_rate_per_hour')) ?> <span style="font-size:.9rem;font-weight:400;">/hr</span></h3>
                <p class="mb-0 text-muted">per hour</p>
            </div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-motorcycle"></i> Bike Rate</h6>
            </div>
            <div class="card-body text-center">
                <h3 class="text-success">₹<?= htmlspecialchars(getSetting('bike_rate_per_hour')) ?> <span style="font-size:.9rem;font-weight:400;">/hr</span></h3>
                <p class="mb-0 text-muted">per hour</p>
            </div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-truck"></i> Truck Rate</h6>
            </div>
            <div class="card-body text-center">
                <h3 class="text-warning">₹<?= htmlspecialchars(getSetting('truck_rate_per_hour')) ?> <span style="font-size:.9rem;font-weight:400;">/hr</span></h3>
                <p class="mb-0 text-muted">per hour</p>
            </div>
        </div>
    </div>
</div>

<!-- Exit Confirmation Modal -->
<div class="modal fade" id="exitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-sign-out-alt"></i> Confirm Vehicle Exit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" id="exit_vehicle_id">
                    <p>Are you sure you want to process exit for:</p>
                    <div class="alert alert-info">
                        <h5 id="exit_vehicle_number" class="mb-2"></h5>
                        <p class="mb-0">Amount to collect: <strong id="exit_amount" class="text-success"></strong></p>
                    </div>
                    <p class="text-muted small">This action will free up the parking slot and record the transaction.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="process_exit" class="btn btn-danger">
                        <i class="fas fa-check"></i> Confirm Exit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function processExit(vehicleId, vehicleNumber, amount) {
    document.getElementById('exit_vehicle_id').value = vehicleId;
    document.getElementById('exit_vehicle_number').textContent = vehicleNumber;
    document.getElementById('exit_amount').textContent = '₹' + amount.toFixed(2);
    
    const exitModal = new bootstrap.Modal(document.getElementById('exitModal'));
    exitModal.show();
}
</script>

<?php require_once 'includes/footer.php'; ?>