
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website Admin dan Anggota</title>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f1f5f9;
      color: #1e293b;
    }

    .sidebar {
      position: fixed;
      top: 0;
      right: 0;
      width: 250px;
      height: 100vh;
      background: #172554;
      color: white;
      padding: 25px 18px;
    }

    .sidebar h2 {
      margin-bottom: 30px;
    }

    .sidebar a {
      display: block;
      padding: 14px;
      margin-bottom: 8px;
      color: white;
      text-decoration: none;
      border-radius: 8px;
    }

    .sidebar a:hover {
      background: #2563eb;
    }

    .content {
      margin-right: 250px;
      padding: 35px;
    }

    .card {
      background: white;
      padding: 25px;
      margin-top: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px #00000010;
    }

    @media (max-width: 600px) {
      .sidebar {
        width: 180px;
      }

      .content {
        margin-right: 180px;
        padding: 15px;
      }
    }
  </style>
</head>

<body>
  <aside class="sidebar">
    <h2>WEB PANEL</h2>
    <div class="menu-pengaduan">
                    <button type="button" class="btn" id="tombolMenu" onclick="toggleMenu()">
                        Menu 
                    </button>

                    <div id="dropdownMenu" class="dropdown-content">
                        <a href="add.php" class="btn btn-tambah-menu">
                            + Tambah Pengaduan
                        </a>

                        <a href="edit_pengaduan.php" class="btn btn-edit-menu">
                           <span class="icon">⚙</span> Edit Pengaduan
                        </a>

                        <a href="hapus_pengaduan.php" class="btn btn-hapus-menu">
                            - Hapus Pengaduan
                        </a>
                    </div>
                </div>
    <a href="index.php">Dashboard</a>
    <a href="login-admin.php">Login Admin</a>
    <a href="login-anggota.php">Login Anggota</a>
    <a href="anggota.php">Data Anggota</a>
  </aside>

  <main class="content">
    <h1>Dashboard Website</h1>
    <p>Selamat datang di halaman utama website.</p>

    <div class="card">
      <h3>Login Admin</h3>
      <p>Admin dapat mengelola data anggota.</p>
    </div>

    <div class="card">
      <h3>Login Anggota</h3>
      <p>Anggota dapat melihat profil dan data miliknya.</p>
    </div>
  </main>
</body>
</html>
.container {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
}

.card {
    display: flex;
    flex-direction: column;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 12px #0000000d;
}