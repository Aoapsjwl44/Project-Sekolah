
<?php
require "koneksi.php";

$nama = "Pe";
$username = "Aoapsjwl";
$password = "Pe4444444";
$hash = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (nama, username, password, role)
     VALUES (?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt, "ssss", $nama, $username, $hash, $role
);

if (mysqli_stmt_execute($stmt)) {
    echo "Akun admin berhasil dibuat. Segera hapus file buat_admin.php.";
} else {
    echo "Gagal membuat akun admin. Periksa apakah username sudah digunakan.";
}
