<?php
/**
 * Dashboard - Main Admin Panel
 * Car Parking Management System
 */
require_once 'includes/header.php';

// Fetch dashboard statistics
$stats = [];

// Total Slots
$stmt = $pdo->query("SELECT COUNT(*) as total FROM parking_slots");
$stats['total_slots'] = $stmt->fetch()['total'];

// Available Slots
$stmt = $pdo->query("SELECT COUNT(*) as available FROM parking_slots WHERE status = 'Available'");
$stats['available_slots'] = $stmt->fetch()['available'];

// Occupied Slots
$stmt = $pdo->query("SELECT COUNT(*) as occupied FROM parking_slots WHERE status = 'Occupied'");
$stats['occupied_slots'] = $stmt->fetch()['occupied'];

// Today's Revenue
$stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as revenue FROM transactions WHERE DATE(created_at) = CURDATE()");
$stats['today_revenue'] = $stmt->fetch()['revenue'];

// Currently Parked Vehicles
$stmt = $pdo->query("SELECT v.*, ps.slot_name FROM vehicles v 
                     JOIN parking_slots ps ON v.slot_id = ps.id 
                     WHERE v.status = 'Parked' 
                     ORDER BY v.entry_time DESC LIMIT 5");
$parked_vehicles = $stmt->fetchAll();

// Recent Activities
$stmt = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10");
$recent_activities = $stmt->fetchAll();

// Get revenue data for last 7 days
$stmt = $pdo->query("SELECT DATE(created_at) as date, SUM(amount) as revenue 
                     FROM transactions 
                     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                     GROUP BY DATE(created_at)
                     ORDER BY date");
$revenue_data = $stmt->fetchAll();
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-dashboard"></i> Dashboard
            </h2>
            <p class="text-muted">Welcome back, <?= htmlspecialchars($user['fullname']) ?> 👋</p>
            <p class="text-muted mb-0">
                <i class="fas fa-clock"></i> <?= date('l, F d, Y - h:i A') ?>
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Slots</p>
                            <h2 class="mb-0 fw-bold" id="totalSlots"><?= $stats['total_slots'] ?></h2>
                        </div>
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-th-large"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Available Slots</p>
                            <h2 class="mb-0 fw-bold text-success" id="availableSlots"><?= $stats['available_slots'] ?></h2>
                        </div>
                        <div class="stats-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Occupied Slots</p>
                            <h2 class="mb-0 fw-bold text-danger" id="occupiedSlots"><?= $stats['occupied_slots'] ?></h2>
                        </div>
                        <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="400">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Today's Revenue</p>
                            <h2 class="mb-0 fw-bold text-info" id="todayRevenue">₹<?= number_format($stats['today_revenue'], 2) ?></h2>
                        </div>
                        <div class="stats-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Revenue Trends (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Slot Occupancy</h5>
                </div>
                <div class="card-body">
                    <canvas id="occupancyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <div class="col-lg-7" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-car"></i> Currently Parked Vehicles</h5>
                    <a href="vehicle_exit.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vehicle No.</th>
                                    <th>Owner</th>
                                    <th>Slot</th>
                                    <th>Entry Time</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($parked_vehicles)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No vehicles currently parked</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($parked_vehicles as $vehicle): 
                                        $duration = floor((time() - strtotime($vehicle['entry_time'])) / 3600);
                                    ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($vehicle['vehicle_number']) ?></strong></td>
                                            <td><?= htmlspecialchars($vehicle['owner_name']) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($vehicle['slot_name']) ?></span></td>
                                            <td><?= date('h:i A', strtotime($vehicle['entry_time'])) ?></td>
                                            <td><?= $duration ?> hrs</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Live Activity Feed</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="activity-feed" id="activityFeed">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item mb-3">
                                <div class="d-flex">
                                    <div class="activity-icon me-3">
                                        <?php if ($activity['activity_type'] == 'entry'): ?>
                                            <i class="fas fa-sign-in-alt text-success"></i>
                                        <?php elseif ($activity['activity_type'] == 'exit'): ?>
                                            <i class="fas fa-sign-out-alt text-danger"></i>
                                        <?php else: ?>
                                            <i class="fas fa-info-circle text-info"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="mb-0"><?= htmlspecialchars($activity['description']) ?></p>
                                        <small class="text-muted"><?= date('h:i A', strtotime($activity['created_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stats-card {
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    
    .activity-feed {
        position: relative;
    }
    
    .activity-item {
        padding-left: 15px;
        border-left: 2px solid #e9ecef;
    }
    
    .activity-icon {
        font-size: 1.2rem;
    }
</style>

<script>
// CountUp Animation for Stats
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CountUp for each stat
    const stats = [
        { id: 'totalSlots', value: <?= $stats['total_slots'] ?> },
        { id: 'availableSlots', value: <?= $stats['available_slots'] ?> },
        { id: 'occupiedSlots', value: <?= $stats['occupied_slots'] ?> }
    ];
    
    stats.forEach(stat => {
        const element = document.getElementById(stat.id);
        if (element && typeof countUp !== 'undefined') {
            const counter = new countUp.CountUp(stat.id, stat.value);
            counter.start();
        }
    });
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: [<?php 
            $labels = [];
            foreach ($revenue_data as $data) {
                $labels[] = "'" . date('M d', strtotime($data['date'])) . "'";
            }
            echo implode(',', $labels);
        ?>],
        datasets: [{
            label: 'Revenue (₹)',
            data: [<?php 
                $values = [];
                foreach ($revenue_data as $data) {
                    $values[] = $data['revenue'];
                }
                echo implode(',', $values);
            ?>],
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Occupancy Chart
const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
const occupancyChart = new Chart(occupancyCtx, {
    type: 'doughnut',
    data: {
        labels: ['Available', 'Occupied'],
        datasets: [{
            data: [<?= $stats['available_slots'] ?>, <?= $stats['occupied_slots'] ?>],
            backgroundColor: ['#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Auto-refresh activity feed every 30 seconds
setInterval(function() {
    fetch('includes/get_activities.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('activityFeed').innerHTML = data;
        });
}, 30000);
</script>

<?php require_once 'includes/footer.php'; ?>