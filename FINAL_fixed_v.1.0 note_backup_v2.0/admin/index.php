<?php
// index.php
include 'fetch.php'; // Memanggil dan mengeksekusi fetch.php
// Memuat konfigurasi base_url dari config.php
$configFile = __DIR__ . '/config.php'; // Path ke file config.php

// Fungsi untuk membaca Base URL
function getBaseUrl() {
    global $configFile;
    if (file_exists($configFile)) {
        include $configFile; // Memuat file config.php
        return $base_url; // Nilai $base_url dari config.php
    }
    return ''; // Default kosong jika config.php tidak ditemukan
}

// Menyimpan Base URL saat ini ke variabel
$currentBaseUrl = getBaseUrl();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}

.navbar {
    margin-bottom: 20px;
}

.navbar-brand {
    font-weight: bold;
}

/* Sidebar styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    background-color: #343a40;
    padding-top: 20px;
    overflow-y: auto;
}

.sidebar a {
    padding: 15px;
    text-decoration: none;
    font-size: 1rem;
    color: white;
    display: block;
}

.sidebar a:hover {
    background-color: #575d63;
}

/* Content styles */
.content {
    margin-left: 250px; 
    padding: 20px;
}

/* Card styles */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 20px; /* Tambahkan margin bawah pada setiap kartu */
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.card-body {
    padding: 20px;
    transition: background-color 0.3s ease;
}

.card:hover .card-body {
    background-color: #f1f1f1;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 500;
    margin-bottom: 15px;
    color: black;
}

.card-text {
    font-size: 1rem; 
    color: #007bff; 
}

/* Chart styles */
#myChart { 
    max-width: 100%; /* Pastikan chart tidak melebihi lebar container */
    height: 300px; /* Atur tinggi chart sesuai kebutuhan */
}

/* Responsive styles */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .sidebar a {
        float: left;
        width: auto; 
    }
    .content {
        margin-left: 0;
    }
}

/* Maintenance Mode Form */
#settings form { /* Targetkan form di dalam section#settings */
    max-width: 400px; /* Batasi lebar maksimum formulir */
    margin: 0 auto;   /* Pusatkan formulir secara horizontal */
}

footer {
    background-color: #f8f9fa;
    padding: 1rem 0;
    font-family: sans-serif;
}

footer .text-muted {
    font-size: 0.9rem;
    line-height: 1.5;
}

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="sidebar">
        <a href="#statistics">Statistics</a>
        <a href="#notes">Notes</a>
        <a href="#settings">Settings</a>
        <a href="#actions">Actions</a>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <section id="statistics" class="mt-5">
      <h1 class="mb-4">Statistics</h1>
      <div class="row">
        <div class="col-md-4">
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Total Requests</h5>
              <p class="card-text"><?php echo number_format($data['total_requests']); ?></p>
              <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#totalRequestsModal">More Info</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Unique IPs</h5>
              <p class="card-text"><?php echo number_format($data['unique_ips']); ?></p>
              <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#uniqueIpsModal">More Info</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Last Visited</h5>
              <p class="card-text"><?php echo htmlspecialchars($data['last_visited']); ?></p>
              <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#lastVisitedModal">More Info</button>
            </div>
          </div>
        </div>
      </div>
    </section>

		<section id="notes" class="mt-5">
			<h2 class="mb-3">Notes</h2>
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Total Notes Created</h5>
					<p class="card-text"><?php echo number_format($noteCount); ?></p>
					<button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#totalNotesModal">More Info</button>
				</div>
			</div>
		</section>

		<!-- Modal for displaying note files -->
		<div class="modal fade" id="totalNotesModal" tabindex="-1" aria-labelledby="totalNotesModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="totalNotesModalLabel">More Info on Total Notes Created</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<ul>
							<?php if (!empty($noteFiles)): ?>
								<?php foreach ($noteFiles as $file): ?>
									<li>
										<a href="https://inote.pw/<?php echo htmlspecialchars($file); ?>" target="_blank">
											<?php echo htmlspecialchars($file); ?>
										</a>
									</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li>No notes found.</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>


<section id="settings" class="mt-5">
    <h2 class="mb-3">Admin Settings</h2>
    <form method="post" action="">
        <!-- Base URL Setting -->
        <div class="mb-3">
            <label for="base_url" class="form-label">Base URL</label>
            <input type="text" class="form-control" id="base_url" name="base_url" 
                   value="<?php echo htmlspecialchars($currentBaseUrl); ?>" 
                   placeholder="Enter Base URL (or leave blank)" required>
        </div>

        <!-- Maintenance Mode Setting -->
        <div class="mb-3">
            <label for="maintenance_mode" class="form-label">Maintenance Mode</label>
            <select class="form-select" id="maintenance_mode" name="maintenance_mode">
                <option value="1" <?php echo $maintenanceMode ? 'selected' : ''; ?>>On</option>
                <option value="0" <?php echo !$maintenanceMode ? 'selected' : ''; ?>>Off</option>
            </select>
        </div>

        <!-- Save Button -->
        <button type="submit" class="btn btn-primary" name="update_settings">Save</button>
    </form>
</section>

    <section id="actions" class="mt-5">
      <h2 class="mb-3">Actions</h2>
      <form method="post" action="">
        <button type="submit" class="btn btn-danger mb-3" name="reset_stats">Reset All Statistics</button>
      </form>
    </section>

        <section id="graph" class="mt-5">
            <h2 class="mb-3">Graphical Statistics</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer mt-5 py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">&copy; <?php echo date("Y"); ?> inote.pw. All rights reserved.</span>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include 'modals.php'; ?> 
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize the Chart.js chart
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Total Requests', 'Unique IPs', 'Total Notes Created'],
        datasets: [{
          label: 'Statistics',
          data: [
            <?php echo number_format($data['total_requests']); ?>,
            <?php echo number_format($data['unique_ips']); ?>,
            <?php echo number_format($noteCount); ?>
          ],
          backgroundColor: [
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  });
</script>

<?php 
if (isset($_POST['maintenance_mode'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: 'Maintenance Mode has been set.'
    });
  </script>
<?php endif; ?>

</body>
</html>
