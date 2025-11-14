<!-- Footer Template -->
<footer class="footer mt-5 py-4 bg-dark text-white">
    <div class="container text-center">
        <p class="mb-2">&copy; 2025 Smart Parking Solutions. All rights reserved.</p>
        <p class="mb-0">
            <small>
                Last Login: <?= isset($user['last_login']) ? date('d M Y, h:i A', strtotime($user['last_login'])) : 'N/A' ?>
            </small>
        </p>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- AOS Animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- CountUp.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/main.js"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Revenue Chart
  const revenueCtx = document.getElementById('revenueChart').getContext('2d');
  new Chart(revenueCtx, {
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
          plugins: { legend: { display: false }},
          scales: { y: { beginAtZero: true } }
      }
  });

  // Occupancy Chart
  const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
  new Chart(occupancyCtx, {
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
          plugins: { legend: { position: 'bottom' }}
      }
  });
});

</script>

</body>
</html>