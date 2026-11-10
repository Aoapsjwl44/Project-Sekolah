php
<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya admin yang boleh mengubah status
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);
$status = $_POST["status"] ?? "";

if ($id <= 0 || !in_array($status, ["belum", "selesai"], true)) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE pengaduan SET status = ? WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "si", $status, $id);
mysqli_stmt_execute($stmt);

header("Location: index.php?pesan=status_berhasil");
exit;
?>