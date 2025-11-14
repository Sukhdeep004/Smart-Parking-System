<?php
/**
 * Reports & Analytics
 * Car Parking Management System
 */
require_once 'includes/header.php';

// Initialize filters
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-7 days'));
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
$vehicle_number = isset($_GET['vehicle_number']) ? cleanInput($_GET['vehicle_number']) : '';
$slot_name = isset($_GET['slot_name']) ? cleanInput($_GET['slot_name']) : '';

// Build query
$query = "SELECT * FROM transactions WHERE 1=1";
$params = [];
if ($date_from) {
    $query .= " AND DATE(created_at) >= ?";
    $params[] = $date_from;
}
if ($date_to) {
    $query .= " AND DATE(created_at) <= ?";
    $params[] = $date_to;
}
if ($vehicle_number) {
    $query .= " AND vehicle_number LIKE ?";
    $params[] = "%$vehicle_number%";
}
if ($slot_name) {
    $query .= " AND slot_name = ?";
    $params[] = $slot_name;
}
$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Stats
$total_transactions = count($transactions);
$total_revenue = array_sum(array_column($transactions, 'amount'));
$avg_duration = $total_transactions > 0 ? array_sum(array_column($transactions, 'duration_hours')) / $total_transactions : 0;

$slots = $pdo->query("SELECT DISTINCT slot_name FROM parking_slots ORDER BY slot_name")->fetchAll();

// CSV Export
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="parking_report_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Transaction ID', 'Vehicle Number', 'Owner Name', 'Slot', 'Entry Time', 'Exit Time', 'Duration (hrs)', 'Amount (₹)', 'Date']);
    foreach ($transactions as $t) {
        fputcsv($output, [
            $t['id'], $t['vehicle_number'], $t['owner_name'], $t['slot_name'], $t['entry_time'], $t['exit_time'],
            $t['duration_hours'], $t['amount'], date('Y-m-d', strtotime($t['created_at']))
        ]);
    }
    fclose($output);
    exit();
}
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-chart-bar"></i> Reports & Analytics</h2>
            <p class="text-muted">View detailed parking reports and statistics</p>
        </div>
    </div>
    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Reports</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" class="form-control" name="vehicle_number" placeholder="Search vehicle" value="<?= $vehicle_number ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Slot</label>
                    <select class="form-select" name="slot_name">
                        <option value="">All Slots</option>
                        <?php foreach ($slots as $slot): ?>
                            <option value="<?= $slot['slot_name'] ?>" <?= $slot_name == $slot['slot_name'] ? 'selected' : '' ?>>
                                <?= $slot['slot_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Apply Filter</button>
                    <a href="reports.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    <a href="?export=1&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>&vehicle_number=<?= $vehicle_number ?>&slot_name=<?= $slot_name ?>" class="btn btn-success"><i class="fas fa-file-csv"></i> Export CSV</a>
                    <button type="button" class="btn btn-info" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-receipt text-primary" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3 mb-0"><?= $total_transactions ?></h3>
                    <p class="text-muted mb-0">Total Transactions</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-rupee-sign text-success" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3 mb-0">₹<?= number_format($total_revenue, 2) ?></h3>
                    <p class="text-muted mb-0">Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock text-info" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-3 mb-0"><?= number_format($avg_duration, 1) ?> hrs</h3>
                    <p class="text-muted mb-0">Avg. Duration</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Transaction History (<?= $total_transactions ?> records)</h5>
        </div>
        <div class="card-body">
        <?php if (empty($transactions)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">No transactions found for the selected filters</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="transactionTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vehicle No.</th>
                            <th>Owner</th>
                            <th>Slot</th>
                            <th>Entry Time</th>
                            <th>Exit Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td><strong><?= htmlspecialchars($t['vehicle_number']) ?></strong></td>
                                <td><?= htmlspecialchars($t['owner_name']) ?></td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($t['slot_name']) ?></span></td>
                                <td><?= date('d M, h:i A', strtotime($t['entry_time'])) ?></td>
                                <td><?= date('d M, h:i A', strtotime($t['exit_time'])) ?></td>
                                <td><?= $t['duration_hours'] ?> hrs</td>
                                <td><strong class="text-success">₹<?= number_format($t['amount'], 2) ?></strong></td>
                                <td><?= date('d M Y', strtotime($t['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="7" class="text-end"><strong>Total:</strong></td>
                            <td><strong class="text-success">₹<?= number_format($total_revenue, 2) ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <!-- Revenue & Slot Charts
    <?php if (!empty($transactions)): ?>
    <div class="row mt-4">
        <div class="col-lg-6" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Daily Revenue</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Top Slots by Usage</h5>
                </div>
                <div class="card-body">
                     <canvas id="slotUsageChart" ></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

Chart Code -->
<?php if (!empty($transactions)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyData = {}, slotData = {};
    <?php foreach ($transactions as $t): ?>
        (() => {
            const date = '<?= date('M d', strtotime($t['created_at'])) ?>';
            const slot = '<?= addslashes($t['slot_name']) ?>';
            const amount = <?= (float)$t['amount'] ?>;
            dailyData[date] = (dailyData[date]||0) + amount;
            slotData[slot] = (slotData[slot]||0) + 1;
        })();
    <?php endforeach; ?>
    // For blank data safety:
    const dailyLabels = Object.keys(dailyData).length ? Object.keys(dailyData) : ['None'];
    const dailyVals   = Object.values(dailyData).length ? Object.values(dailyData) : [0];
    const slotLabels  = Object.keys(slotData).length ? Object.keys(slotData) : ['None'];
    const slotVals    = Object.values(slotData).length ? Object.values(slotData) : [0];
    if (document.getElementById('dailyRevenueChart')) {
        new Chart(document.getElementById('dailyRevenueChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: dailyVals,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                    borderColor: 'rgb(13, 110, 253)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
    if (document.getElementById('slotUsageChart')) {
        new Chart(document.getElementById('slotUsageChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: slotLabels,
                datasets: [{
                    data: slotVals,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ]
                }]
            },
            options: { responsive: true }
        });
    }
});
</script>
<?php endif; ?>

<style>
@media print {
    .navbar, .card-header button, .btn, form { display: none !important; }
    .card { box-shadow: none !important; }
}
</style>
<?php require_once 'includes/footer.php'; ?>
