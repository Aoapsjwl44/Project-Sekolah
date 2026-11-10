
<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

function e($data) {
    return htmlspecialchars((string) $data, ENT_QUOTES, "UTF-8");
}

$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$userId = (int) $_SESSION["user_id"];

if ($isAdmin) {
    $sql = "SELECT * FROM pengaduan ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
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
    <title>Edit Pengaduan</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="edit2.css"> 
</head>
<body>
<div class="container">
    <div class="layout-utama">

        <main class="layout-konten">
            <div class="card tengah">

            <h1>Edit Pengaduan Fasilitas Sekolah</h1>
            <p>
                <?= $isAdmin
                    ? "Pilih pengaduan yang ingin diedit."
                    : "Pilih pengaduan milikmu yang ingin diedit." ?>
            </p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Fasilitas</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= e($row["nama_siswa"]) ?></td>
                                <td><?= e($row["kelas"]) ?></td>
                                <td><?= e($row["fasilitas"]) ?></td>
                                <td><?= e($row["keterangan"]) ?></td>
                                <td><?= e($row["tanggal"]) ?></td>
                                <td>
                                    <a
                                        href="edit.php?id=<?= (int) $row["id"] ?>"
                                        class="btn-edit-menu-i"
                                    ><i class="bi bi-pencil-square" style="text-align: center;"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                Belum ada pengaduan yang bisa diedit.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            </div>
        </main>

        <?php require "sidebar.php"; ?>

    </div>
</div>

</body>
</html>


