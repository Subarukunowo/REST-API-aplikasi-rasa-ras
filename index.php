<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <div class="sidebar">
            <div class="admin-profile">
                <img src="images/patrick_kluivert.jpg" alt="Admin Profile" class="profile-pic">
                <span class="profile-name">Patrick Kluivert</span>
                <div class="search-box">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="data_pengguna.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'data_pengguna.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Data Pengguna</a></li>
                <li><a href="data_resep.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'data_resep.php') ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Data Resep</a></li>
                <li><a href="notifikasi.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'notifikasi.php') ? 'active' : ''; ?>"><i class="fas fa-bell"></i> Notifikasi</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="navbar-top">
                <div class="breadcrumbs">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <a href="#">Contact</a>
                </div>
                <div class="navbar-icons">
                    <i class="fas fa-envelope"></i>
                    <i class="fas fa-bell"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-cog"></i>
                </div>
            </div>
            <div class="content-header">
                <h2><?php echo $content_title ?? 'Dashboard'; ?></h2>
            </div>
            <div class="content-body">
                </div> </div> </div> <script src="js/script.js"></script>
</body>
</html>
<?php
$page_title = 'Dashboard';
$content_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="dashboard-grid">
    <div class="card-recipe-category">
        <h3>Resep Sarapan</h3>
        <img src="images/sarapan.jpg" alt="Sarapan">
    </div>
    <div class="card-recipe-category">
        <h3>Resep Makan Siang</h3>
        <img src="images/makan_siang.jpg" alt="Makan Siang">
    </div>
    <div class="card-recipe-category">
        <h3>Resep Makan Malam</h3>
        <img src="images/makan_malam.jpg" alt="Makan Malam">
    </div>
    <div class="card-recipe-category">
        <h3>Resep Hari Raya</h3>
        <img src="images/hari_raya.jpg" alt="Hari Raya">
    </div>
    <div class="card-recipe-category">
        <h3>Resep Makanan</h3>
        <img src="images/makanan.jpg" alt="Makanan">
    </div>

    <div class="card out-of-stock">
        <h4>Out of Stock</h4>
        <span class="count">56</span>
        <a href="#">Show Details</a>
    </div>

    <div class="card total-profit">
        <h4>Total Profit</h4>
        <span class="amount">$456</span>
    </div>

    <div class="card total-sales">
        <h4>Total Sals</h4>
        <span class="count">234</span>
    </div>

    <div class="card total-customer">
        <h4>Total Customer</h4>
        <span class="count">120</span>
    </div>

    <div class="card popular-recipes">
        <h3>Resep Populer</h3>
        <div class="recipe-item">
            <img src="images/rendang.jpg" alt="Rendang Daging">
            <div class="recipe-info">
                <h4>Rendang Daging</h4>
                <div class="stars">
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span class="cook-time">Malakat terofil</span>
            </div>
            <div class="cooked-count">
                <span>16.1K</span>
                <span>Cooked</span>
            </div>
        </div>
        <div class="recipe-item">
            <img src="images/sate_madura.jpg" alt="Sate Madura">
            <div class="recipe-info">
                <h4>Sate Madura</h4>
                <div class="stars">
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                    <i class="fas fa-star full"></i>
                </div>
                <span class="cook-time">Malakat terofil</span>
            </div>
            <div class="cooked-count">
                <span>16.1K</span>
                <span>Cooked</span>
            </div>
        </div>
    </div>

    <div class="card chart-container">
        <canvas id="myChart"></canvas>
    </div>
</div>

<script>
    // Data for the chart
    const labels = ['2011 Q1', '2011 Q2', '2011 Q3', '2011 Q4', '2012 Q1', '2012 Q2', '2012 Q3', '2012 Q4', '2013 Q1', '2013 Q2'];
    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Sopir',
                data: [5000, 7000, 6000, 8000, 9000, 11000, 10000, 13000, 12000, 15000], // Example data
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.4,
                fill: false
            },
            {
                label: 'Penumpang',
                data: [4000, 6000, 5000, 7000, 8000, 10000, 9000, 12000, 11000, 14000], // Example data
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.4,
                fill: false
            },
            {
                label: 'Tiket',
                data: [3000, 5000, 4000, 6000, 7000, 9000, 8000, 11000, 10000, 13000], // Example data
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.4,
                fill: false
            }
        ]
    };

    // Configuration for the chart
    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Create the chart
    var myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
</script>

<?php include 'includes/footer.php'; ?>
<?php
$page_title = 'Mengelola data Pengguna';
$content_title = 'Data Pengguna';
// Ubah nama profil di sidebar jika perlu
// $profile_name = 'Najua Rahmah';
include 'includes/header.php';
?>

<div class="data-table-section">
    <div class="table-actions">
        <button class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</button>
        <div class="search-sort">
            <div class="search-box-table">
                <input type="text" placeholder="Search...">
                <i class="fas fa-search"></i>
            </div>
            <div class="sort-dropdown">
                <span>Short by:</span>
                <select>
                    <option>Newest</option>
                    <option>Oldest</option>
                    <option>Name (A-Z)</option>
                </select>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Pengguna</th>
                <th>Alamat Pengguna</th>
                <th>Nomor Hp</th>
                <th>Email Pengguna</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sandy Mulia Kesuma</td>
                <td>Wonosari Tengah</td>
                <td>0852-1614-6411</td>
                <td>sandymulia@gmail.com</td>
                <td class="actions">
                    <button class="btn btn-detail">Detail</button>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Hapus</button>
                </td>
            </tr>
            <tr>
                <td>Nuraulia Octaviana</td>
                <td>Jl.Pramuka</td>
                <td>0822-8618-7418</td>
                <td>nuraulia@gmail.com</td>
                <td class="actions">
                    <button class="btn btn-detail">Detail</button>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Hapus</button>
                </td>
            </tr>
            <tr>
                <td>Najua Rahmah Putri</td>
                <td>Jl.Pramuka Gg.Delima</td>
                <td>0822-8958-0358</td>
                <td>najuarahmahputri@gmail.com</td>
                <td class="actions">
                    <button class="btn btn-detail">Detail</button>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Hapus</button>
                </td>
            </tr>
            </tbody>
    </table>

    <div class="table-pagination">
        <span class="showing-info">Showing data 1 to 8 of 256K entries</span>
        <div class="pagination-controls">
            <a href="#" class="prev-next"><i class="fas fa-chevron-left"></i></a>
            <a href="#" class="page-num active">1</a>
            <a href="#" class="page-num">2</a>
            <a href="#" class="page-num">3</a>
            <a href="#" class="page-num">4</a>
            <span>...</span>
            <a href="#" class="page-num">40</a>
            <a href="#" class="prev-next"><i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
$page_title = 'Mengelola Data Resep';
$content_title = 'Data Resep';
include 'includes/header.php';
?>

<div class="data-table-section">
    <div class="table-actions">
        <button class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</button>
        <div class="search-sort">
            <div class="search-box-table">
                <input type="text" placeholder="Search...">
                <i class="fas fa-search"></i>
            </div>
            <div class="sort-dropdown">
                <span>Short by:</span>
                <select>
                    <option>Newest</option>
                    <option>Oldest</option>
                    <option>Name (A-Z)</option>
                </select>
            </div>
        </div>
    </div>

    <table class="data-table recipe-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Judul Resep</th>
                <th>Kategori</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><img src="images/ikan_bakar_madu.jpg" alt="Ikan Bakar Madu" class="recipe-thumbnail"></td>
                <td>Ikan Bakar Madu</td>
                <td>Makanan</td>
                <td>1 Jam</td>
                <td class="actions">
                    <button class="btn btn-detail">Detail</button>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Hapus</button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td><img src="images/ikan_goreng_madu.jpg" alt="Ikan Goreng Madu" class="recipe-thumbnail"></td>
                <td>Ikan Goreng Madu</td>
                <td>Makanan</td>
                <td>1 Jam</td>
                <td class="actions">
                    <button class="btn btn-detail">Detail</button>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Hapus</button>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td><img src="images/ikan_pindang.jpg" alt="Ikan Pindang" class="recipe-thumbnail"></td>
                <td>Ikan Pindang</td>
                <td>Makanan</td>
                <td>1 Jam</td>
                <td class="actions">
                    <button class="btn btn-detail">Detail</button>
                    <button class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete">Hapus</button>
                </td>
            </tr>
            </tbody>
    </table>

    <div class="table-pagination">
        <span class="showing-info">Showing data 1 to 8 of 256K entries</span>
        <div class="pagination-controls">
            <a href="#" class="prev-next"><i class="fas fa-chevron-left"></i></a>
            <a href="#" class="page-num active">1</a>
            <a href="#" class="page-num">2</a>
            <a href="#" class="page-num">3</a>
            <a href="#" class="page-num">4</a>
            <span>...</span>
            <a href="#" class="page-num">40</a>
            <a href="#" class="prev-next"><i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
$page_title = 'Notifikasi';
$content_title = 'Notifikasi';
// Ubah nama profil di sidebar jika perlu
// $profile_name = 'Najua Rahmah';
include 'includes/header.php';
?>

<div class="notification-list">
    <div class="notification-card recommendation">
        <div class="notification-icon"><i class="fas fa-utensils"></i></div>
        <div class="notification-content">
            <h4>Rekomendasi Hari Ini:</h4>
            <p>Coba resep Sate Ayam Bumbu Kacang untuk makan malam yang lezat!</p>
        </div>
    </div>

    <div class="notification-card favorite-recipe">
        <div class="notification-icon"><i class="fas fa-heart"></i></div>
        <div class="notification-content">
            <h4>Resep Terfavorit:</h4>
            <p>"Nasi Goreng Jawa Spesial" jadi resep paling banyak dicoba minggu ini!</p>
        </div>
    </div>

    <div class="notification-card shopping-reminder">
        <div class="notification-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="notification-content">
            <h4>Peringat Belanja:</h4>
            <p>Kamu belum membeli/bahan utama untuk resep Sayur Asem. Cek daftarnya!</p>
        </div>
    </div>

    <div class="notification-card saved-favorite">
        <div class="notification-icon"><i class="fas fa-star"></i></div>
        <div class="notification-content">
            <h4>Favorit Tersimpan:</h4>
            <p>Resep "Spaghetti Carbonara" berhasil ditambahkan ke daftar favoritmu</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>