
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function wajibLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function wajibAdmin(): void
{
    wajibLogin();

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Akses ditolak. Halaman ini hanya untuk admin.');
    }
}
