
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function eSidebar($data): string {
    return htmlspecialchars((string) $data, ENT_QUOTES, "UTF-8");
}

$namaSidebar = $_SESSION["nama"] ?? "Pengguna";
$roleSidebar = $_SESSION["role"] ?? "anggota";
$isAdminSidebar = $roleSidebar === "admin";
?>
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<aside class="sidebar sidebar-kanan">

    <!-- LOGO SEKOLAH -->
    <div class="brand">
        <img src="2.png" alt="Logo sekolah">

        <div>
            <strong>SMK Nurul Islam</strong>
            <small>Fasilitas Sekolah</small>
        </div>
    </div>

    <!-- PROFIL PENGGUNA -->
    <div class="profile">
        <div class="avatar">
            <i class="bi bi-person-fill"></i>
        </div>

        <div class="profile-info">
            <strong><?= eSidebar($namaSidebar) ?></strong>

            <small>
                <?= $isAdminSidebar ? "Administrator" : "Anggota" ?>
            </small>
        </div>
    </div>

    <p class="nav-label">MENU UTAMA</p>

    <!-- DASHBOARD -->
    <a href="index.php" class="nav-link">
        <i class="bi bi-grid-1x2-fill"></i>
        Dashboard
    </a>

    <!-- TAMBAH -->
    <a href="add.php" class="nav-link nav-tambah">
        <i class="bi bi-plus-circle"></i>
        Tambah Pengaduan
    </a>

    <!-- EDIT -->
    <a href="edit_pengaduan.php" class="nav-link nav-edit">
        <i class="bi bi-pencil-square"></i>
        Edit Pengaduan
    </a>

    <!-- HAPUS -->
    <a href="hapus_pengaduan.php" class="nav-link nav-hapus">
        <i class="bi bi-trash3"></i>
        Hapus Pengaduan
    </a>

    <!-- LOGOUT DI BAGIAN BAWAH -->
    <div class="sidebar-bottom">
        <a
            href="logout.php"
            class="nav-link logout"
            onclick="return confirm('Yakin ingin logout?')"
        >
            <i class="bi bi-box-arrow-left"></i>
            Logout
        </a>
    </div>

</aside>
