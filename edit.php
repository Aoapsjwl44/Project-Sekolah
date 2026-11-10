
<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

function e($data) {
    return htmlspecialchars((string) $data, ENT_QUOTES, "UTF-8");
}

$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$userId = (int) $_SESSION["user_id"];

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    http_response_code(400);
    exit("ID pengaduan tidak valid.");
}

function ambilPengaduan($conn, $id, $isAdmin, $userId) {
    if ($isAdmin) {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM pengaduan WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "i", $id);
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM pengaduan WHERE id = ? AND user_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $data;
}

$pengaduan = ambilPengaduan($conn, $id, $isAdmin, $userId);

if (!$pengaduan) {
    http_response_code(404);
    exit("Pengaduan tidak ditemukan atau kamu tidak memiliki akses.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_siswa = trim($_POST["nama_siswa"] ?? "");
    $kelas = trim($_POST["kelas"] ?? "");
    $fasilitas = trim($_POST["fasilitas"] ?? "");
    $keterangan = trim($_POST["keterangan"] ?? "");
    

 

    if (
        $nama_siswa === "" ||
        $kelas === "" ||
        $fasilitas === "" ||
        $keterangan === "" 
       
    ) {
        $error = "Semua kolom wajib diisi dengan benar.";
    } else {
        if ($isAdmin) {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE pengaduan
                 SET nama_siswa = ?, kelas = ?, fasilitas = ?,
                     keterangan = ?, tanggal = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssssi",
                $nama_siswa,
                $kelas,
                $fasilitas,
                $keterangan,
                $tanggal,
                $id
            );
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE pengaduan
                 SET nama_siswa = ?, kelas = ?, fasilitas = ?,
                     keterangan = ?, tanggal = ?
                 WHERE id = ? AND user_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssssii",
                $nama_siswa,
                $kelas,
                $fasilitas,
                $keterangan,
                $tanggal,
                $id,
                $userId
            );
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: edit_pengaduan.php?pesan=edit_berhasil");
            exit;
        }

        mysqli_stmt_close($stmt);
        $error = "Pengaduan gagal diperbarui. Silakan coba lagi.";
    }

    // Pertahankan nilai yang sedang diedit jika validasi gagal.
    $pengaduan["nama_siswa"] = $nama_siswa;
    $pengaduan["kelas"] = $kelas;
    $pengaduan["fasilitas"] = $fasilitas;
    $pengaduan["keterangan"] = $keterangan;
    $pengaduan["tanggal"] = $tanggal;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Pengaduan | Panel Sekolah</title>

    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="edit.css">
</head>

<body>

<div class="container">
    <div class="card">
        <main class="tengah form-pengaduan">
            <h1>Edit Pengaduan Fasilitas Sekolah</h1>

            <?php if ($error !== ""): ?>
                <p class="pesan-gagal"><?= e($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="edit.php">
                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <label for="nama_siswa">Nama Siswa</label>
                <input
                    type="text"
                    id="nama_siswa"
                    name="nama_siswa"
                    value="<?= e($pengaduan["nama_siswa"]) ?>"
                    required
                >

                <label for="kelas">Kelas</label>
                <input
                    type="text"
                    id="kelas"
                    name="kelas"
                    value="<?= e($pengaduan["kelas"]) ?>"
                    required
                >

                <label for="fasilitas">Fasilitas</label>
                <input
                    type="text"
                    id="fasilitas"
                    name="fasilitas"
                    value="<?= e($pengaduan["fasilitas"]) ?>"
                    required
                >

                <label for="keterangan">Keterangan</label>
                <textarea
                    id="keterangan"
                    name="keterangan"
                    required
                ><?= e($pengaduan["keterangan"]) ?></textarea>

                

                <div class="form-actions">
                    
                    <a href="edit_pengaduan.php" class="btn btn-kembali">
                       <i class="bi bi-x-lg"></i> CANCEL
                    </a>
                    <button type="submit" class="btn btn-edit-menu-p">
                        <i class="bi bi-floppy"></i> SAVE
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>

</body>
</html>
