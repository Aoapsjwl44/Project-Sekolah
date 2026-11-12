
<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($data): string {
    return htmlspecialchars((string) $data, ENT_QUOTES, "UTF-8");
}

$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$userId = (int) ($_SESSION["user_id"] ?? 0);

if ($isAdmin) {
    $sql = "SELECT p.*, u.nama AS nama_pelapor
            FROM pengaduan p
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.id DESC";

    $result = mysqli_query($conn, $sql);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT p.*, u.nama AS nama_pelapor
         FROM pengaduan p
         LEFT JOIN users u ON p.user_id = u.id
         WHERE p.user_id = ?
         ORDER BY p.id DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

$total = $result ? mysqli_num_rows($result) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel Admin | Pengaduan Sekolah</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="index.css">
</head>
<body>

<div class="app">

    <!-- KONTEN UTAMA DI KIRI -->
    <main class="main">

        <header class="topbar">
            <div>
                <h1>Dashboard Pengaduan Fasilitas</h1>
                <p>Kelola laporan fasilitas sekolah dengan mudah.</p>
            </div>

            <div class="top-user">
                <i class="bi bi-shield-check"></i>
                <?= $isAdmin ? "Admin / Operator" : "Anggota / Pengadu" ?>
            </div>
        </header>

        <section class="welcome">
            <div>
                <span class="eyebrow">PANEL PENGELOLAAN</span>

                <h2>
                    Selamat datang,
                    <?= e($_SESSION["nama"] ?? "Pengguna") ?>!
                </h2>

                <p>
                    <?= $isAdmin
                        ? "Pantau dan kelola seluruh pengaduan fasilitas sekolah."
                        : "Pantau dan kelola pengaduan yang telah kamu buat." ?>
                </p>
            </div>

            <div class="welcome-icon">
                <i class="bi bi-buildings"></i>
            </div>
        </section>

        <?php if (isset($_GET["sukses"])): ?>
            <div class="alert success">
                <i class="bi bi-check-circle-fill"></i>
                Pengaduan berhasil ditambahkan.
            </div>
        <?php endif; ?>

        <?php if (($_GET["pesan"] ?? "") === "hapus_berhasil"): ?>
            <div class="alert success">
                <i class="bi bi-check-circle-fill"></i>
                Pengaduan berhasil dihapus.
            </div>
        <?php endif; ?>

        <?php if (($_GET["pesan"] ?? "") === "edit_berhasil"): ?>
            <div class="alert success">
                <i class="bi bi-check-circle-fill"></i>
                Pengaduan berhasil diperbarui.
            </div>
        <?php endif; ?>

        <section class="stats">

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div>
                    <span>Total Pengaduan</span>
                    <h3><?= $total ?></h3>
                    <small>
                        <?= $isAdmin
                            ? "Seluruh laporan masuk"
                            : "Laporan milik kamu" ?>
                    </small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <span>Login Sebagai</span>
                    <h3><?= $isAdmin ? "Admin" : "Anggota" ?></h3>
                    <small>Akun aktif saat ini</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <span>Tanggal Hari Ini</span>
                    <h3 class="date-value"><?= date("d/m/Y") ?></h3>
                    <small>Waktu server</small>
                </div>
            </div>

        </section>

        <section class="table-card">

            
<div class="table-heading">
    <div>
        <h2>Daftar Pengaduan</h2>
        <p>Data laporan fasilitas sekolah</p>
    </div>

    <div class="table-actions">

        <a href="export_pengaduan.php" class="btn-export">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        Ekspor Excel
        </a>

    </div>
</div>


            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>

                            <?php if ($isAdmin): ?>
                                <th>Pelapor</th>
                            <?php endif; ?>

                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Fasilitas</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Status</th>

                            <?php if ($isAdmin): ?>
                            <th>Aksi Status</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($result && $total > 0): ?>
                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $pemilik = (int) ($row["user_id"] ?? 0) === $userId;
                            $bolehKelola = $isAdmin || $pemilik;
                            ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <?php if ($isAdmin): ?>
                                    <td>
                                        <div class="reporter">
                                            <span class="reporter-icon">
                                                <i class="bi bi-person"></i>
                                            </span>

                                            <?= e($row["nama_pelapor"] ?? "-") ?>
                                        </div>
                                    </td>
                                <?php endif; ?>

                                <td><?= e($row["nama_siswa"] ?? "-") ?></td>

                                <td>
                                    <span class="class-badge">
                                        <?= e($row["kelas"] ?? "-") ?>
                                    </span>
                                </td>

                                <td><?= e($row["fasilitas"] ?? "-") ?></td>

                                <td class="description">
                                    <?= e($row["keterangan"] ?? "-") ?>
                                </td>

                                <td><?= e($row["tanggal"] ?? "-") ?></td>

                                <td>
                                <?php if (($row["status"] ?? "belum") === "selesai"): ?>
                                <span class="status-selesai" title="Sudah ditangani">
                                <i class="bi bi-check-circle-fill"></i>
                                </span>
                                <?php else: ?>
                                <span class="status-belum" title="Belum ditangani">
                                -
                                </span>
                                <?php endif; ?>
                                </td>
                                        <?php if ($isAdmin): ?>
                                <td>
                                        <form action="ubah_status.php" method="POST">

                                            <input type="hidden" name="id"
                                                value="<?= (int) $row['id'] ?>">

                                            <input type="hidden" name="status"
                                                value="<?= ($row['status'] ?? 'belum') === 'selesai' ? 'belum' : 'selesai' ?>">

                                            <button type="submit"
                                                    class="btn-status"
                                                    onclick="return confirm('Yakin ingin mengubah status laporan ini?')">

                                                <?php if (($row['status'] ?? 'belum') === 'selesai'): ?>
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    Batalkan
                                                <?php else: ?>
                                                    <i class="bi bi-check-circle"></i>
                                                    Tandai Selesai
                                                <?php endif; ?>

                                            </button>

                                        </form>
                                </td>
                                <?php endif; ?>
                              
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? 8 : 7 ?>" class="empty">
                                <i class="bi bi-inbox"></i>
                                <strong>Belum ada pengaduan</strong>
                                <span>Data pengaduan akan muncul di sini.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                Menampilkan <?= $total ?> pengaduan
            </div>

        </section>

        <footer>
            &copy; <?= date("Y") ?> Sistem Pengaduan Fasilitas Sekolah
        </footer>

    </main>

    <!-- SIDEBAR DI KANAN -->
    <aside class="sidebar">

        <div class="brand">
            <img src="2.png" alt="Logo sekolah">

            <div>
                <strong>SMK Nurul Islam</strong>
                <small>Fasilitas Sekolah</small>
            </div>
        </div>

        <div class="profile">
            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="profile-info">
                <strong><?= e($_SESSION["nama"] ?? "Pengguna") ?></strong>
                <small>
                    <?= $isAdmin ? "Administrator" : "Anggota" ?>
                </small>
            </div>
        </div>

        <p class="nav-label">MENU UTAMA</p>

        <a href="index.php" class="nav-link active">
            <i class="bi bi-grid-1x2-fill"></i>
            Dashboard
        </a>

        <a href="add.php" class="nav-link nav-tambah">
            <i class="bi bi-plus-circle"></i>
            Tambah Pengaduan
        </a>

        <a href="edit_pengaduan.php" class="nav-link nav-edit">
            <i class="bi bi-pencil-square"></i>
            Edit Pengaduan
        </a>

        <a href="hapus_pengaduan.php" class="nav-link nav-hapus">
            <i class="bi bi-trash3"></i>
            Hapus Pengaduan
        </a>

        <div class="sidebar-bottom">
            <a href="logout.php"
               class="nav-link logout"
               onclick="return confirm('Yakin ingin logout?')">

                <i class="bi bi-box-arrow-left"></i>
                Logout
            </a>
        </div>

    </aside>

</div>

</body>
</html>
