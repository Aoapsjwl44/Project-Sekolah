
<?php
session_start();
require "koneksi.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

$nama = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($nama === "" || $username === "" || strlen($password) < 8) {
        $error = "Isi semua data. Password minimal 8 karakter.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = "anggota";

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users (nama, username, password, role)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nama,
            $username,
            $hash,
            $role
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: login.php");
            exit;
        }

        $error = mysqli_stmt_errno($stmt) === 1062
            ? "Username sudah digunakan."
            : "Pendaftaran gagal. Silakan coba lagi.";

        mysqli_stmt_close($stmt);
    }
}

function e($data): string {
    return htmlspecialchars((string) $data, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Daftar Anggota | Pengaduan Sekolah</title>

    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="halaman-register">

    <div class="register-container">

        <div class="register-card">

            <div class="logo">
                <img src="2.png" alt="Logo Sekolah">
            </div>

            <h1>Daftar Anggota</h1>

            <p class="register-deskripsi">
                Buat akun untuk mengirim pengaduan sekolah.
            </p>

            <?php if ($error !== ""): ?>
                <p class="pesan" role="alert">
                    <?= e($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="register.php">

                <label for="nama">
                    <i class="bi bi-person"></i>
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    maxlength="100"
                    placeholder="Masukkan nama lengkap"
                    value="<?= e($nama) ?>"
                    autocomplete="name"
                    required
                >

                <label for="username">
                    <i class="bi bi-person-badge"></i>
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    maxlength="50"
                    placeholder="Buat username"
                    value="<?= e($username) ?>"
                    autocomplete="username"
                    required
                >

                <label for="password">
                    <i class="bi bi-lock"></i>
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    required
                >

                <button
                    class="register-button"
                    type="submit"
                >
                    <i class="bi bi-person-plus-fill"></i>
                    SIGN UP
                </button>

            </form>

            <p class="register-login">
                Sudah punya akun?
                <a href="login.php">Login</a>
            </p>

            <div class="login-kembali">
                <a href="index.php">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke halaman utama
                </a>
            </div>

        </div>

    </div>

</body>
</html>
