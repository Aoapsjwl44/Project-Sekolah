<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($data) {
    return htmlspecialchars((string) $data, ENT_QUOTES, "UTF-8");
}

$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$userId = (int) ($_SESSION["user_id"] ?? 0);

// =====================================
// PROSES HAPUS PENGADUAN
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // HAPUS SEMUA KHUSUS ADMIN
    if (isset($_POST["hapus_semua"])) {

        if (!$isAdmin) {
            header("Location: hapus_pengaduan.php?pesan=gagal");
            exit;
        }

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM pengaduan"
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: hapus_pengaduan.php?pesan=semua_berhasil");
        exit;
    }

    // HAPUS SATU PENGADUAN
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        header("Location: hapus_pengaduan.php?pesan=gagal");
        exit;
    }

    if ($isAdmin) {
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM pengaduan WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

    } else {
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM pengaduan WHERE id = ? AND user_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
    }

    mysqli_stmt_execute($stmt);

    $berhasil = mysqli_stmt_affected_rows($stmt) > 0;

    mysqli_stmt_close($stmt);

    header(
        "Location: hapus_pengaduan.php?pesan=" .
        ($berhasil ? "hapus_berhasil" : "gagal")
    );

    exit;
}

// =====================================
// AMBIL DATA PENGADUAN
// =====================================

if ($isAdmin) {

    $result = mysqli_query(
        $conn,
        "SELECT * FROM pengaduan ORDER BY id DESC"
    );

} else {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM pengaduan
         WHERE user_id = ?
         ORDER BY id DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hapus Pengaduan</title>

    <link rel="stylesheet" href="index.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="delete.css">
</head>

<body>

<div class="container">

    <div class="layout-utama">

        <main class="layout-konten">

            <div class="card tengah">

                <!-- JUDUL DAN TOMBOL HAPUS SEMUA -->
                <div class="judul-hapus">

                    <h1>Hapus Pengaduan</h1>

                    <?php if ($isAdmin && $result && mysqli_num_rows($result) > 0): ?>

                        <form
                            method="POST"
                            action="hapus_pengaduan.php"
                            onsubmit="return confirm('PERINGATAN! Yakin ingin menghapus SEMUA pengaduan? Data yang dihapus tidak dapat dikembalikan!')"
                        >

                            <input
                                type="hidden"
                                name="hapus_semua"
                                value="1"
                            >

                            <button
                                type="submit"
                                class="btn-hapus-semua"
                            >
                                <i class="bi bi-trash3-fill"></i>
                                DELETE ALL
                            </button>

                        </form>

                    <?php endif; ?>

                </div>

                <p>
                    <?= $isAdmin
                        ? "Pilih laporan yang ingin dihapus."
                        : "Kamu hanya bisa menghapus laporan milikmu sendiri." ?>
                </p>

                <!-- NOTIFIKASI -->
                <?php if (($_GET["pesan"] ?? "") === "semua_berhasil"): ?>

                    <p class="pesan">
                        <i class="bi bi-check-circle-fill"></i>
                        Semua pengaduan berhasil dihapus!
                    </p>

                <?php elseif (($_GET["pesan"] ?? "") === "hapus_berhasil"): ?>

                    <p class="pesan">
                        <i class="bi bi-check-circle-fill"></i>
                        Pengaduan berhasil dihapus!
                    </p>

                <?php elseif (($_GET["pesan"] ?? "") === "gagal"): ?>

                    <p class="pesan-gagal">
                        Data tidak ditemukan atau kamu tidak memiliki akses.
                    </p>

                <?php endif; ?>

                <!-- DAFTAR PENGADUAN -->
                <?php if ($result && mysqli_num_rows($result) > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <div class="konfirmasi-hapus">

                            <p>
                                <strong>Nama Siswa:</strong>
                                <?= e($row["nama_siswa"] ?? "-") ?>
                            </p>

                            <p>
                                <strong>Kelas:</strong>
                                <?= e($row["kelas"] ?? "-") ?>
                            </p>

                            <p>
                                <strong>Fasilitas:</strong>
                                <?= e($row["fasilitas"] ?? "-") ?>
                            </p>

                            <p>
                                <strong>Keterangan:</strong>
                                <?= e($row["keterangan"] ?? "-") ?>
                            </p>

                            <p>
                                <strong>Tanggal:</strong>
                                <?= e($row["tanggal"] ?? "-") ?>
                            </p>

                            <!-- TOMBOL HAPUS SATU -->
                            <form
                                method="POST"
                                action="hapus_pengaduan.php"
                                onsubmit="return confirm('Yakin ingin menghapus pengaduan ini?')"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $row["id"] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-hapus-menu"
                                >
                                    <i class="bi bi-trash3"></i>
                                    DELETE
                                </button>

                            </form>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <p>Belum ada pengaduan yang bisa dihapus.</p>

                <?php endif; ?>

            </div>

        </main>

        <?php require "sidebar.php"; ?>

    </div>

</div>

</body>
</html>
