
<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

$pesan = "";
$nama_siswa = "";
$kelas = "";
$fasilitas = "";
$keterangan = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_siswa = trim($_POST["nama_siswa"] ?? "");
    $kelas = trim($_POST["kelas"] ?? "");
    $fasilitas = trim($_POST["fasilitas"] ?? "");
    $keterangan = trim($_POST["keterangan"] ?? "");

    $daftarKelas = [
        "X PPLG", "XI PPLG", "XII PPLG",
        "X TKJ", "XI TKJ", "XII TKJ",
        "X TO", "XI TO", "XII TO",
        "X DKV", "XI DKV", "XII DKV",
        "X MPLB", "XI MPLB", "XII MPLB"
    ];

    $daftarFasilitas = [
        "Meja", "Kursi", "Papan Tulis", "Lampu",
        "Pendingin Udara", "Pintu", "Jendela",
        "Infocus", "Komputer"
    ];

    if (
        $nama_siswa === "" ||
        $kelas === "" ||
        $fasilitas === "" ||
        $keterangan === ""
    ) {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (
        !in_array($kelas, $daftarKelas, true) ||
        !in_array($fasilitas, $daftarFasilitas, true)
    ) {
        $pesan = "Pilihan kelas atau fasilitas tidak valid.";
    } else {
        $userId = (int) $_SESSION["user_id"];
        $tanggal = date("Y-m-d");

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO pengaduan
            (nama_siswa, kelas, fasilitas, keterangan, tanggal, user_id)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $nama_siswa,
            $kelas,
            $fasilitas,
            $keterangan,
            $tanggal,
            $userId
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php?sukses=1");
            exit;
        } else {
            $pesan = "Pengaduan gagal disimpan. Silakan coba lagi.";
            mysqli_stmt_close($stmt);
        }
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

    <title>Tambah Pengaduan | Panel Sekolah</title>

    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="add.css">
</head>

<body>

<div class="app">

    <main class="main">

        <header class="topbar">
            <div>
                <h1>Tambah Pengaduan</h1>
                <p>Laporkan kerusakan fasilitas sekolah melalui formulir ini.</p>
            </div>

            <div class="top-user">
                <i class="bi bi-person-check"></i>
                <?= e($_SESSION["nama"] ?? "Pengguna") ?>
            </div>
        </header>

        <div class="form-page">

            <section class="add-banner">
                <div class="add-banner-icon">
                    <i class="bi bi-clipboard-plus"></i>
                </div>

                <div>
                    <span>FORMULIR PELAPORAN</span>
                    <h2>Laporkan Fasilitas Rusak</h2>
                    <p>
                        Isi informasi dengan benar agar laporan
                        dapat ditindaklanjuti.
                    </p>
                </div>
            </section>

            <section class="add-card">

                <div class="add-card-header">
                    <div class="add-title-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>

                    <div>
                        <h2>Data Pengaduan</h2>
                        <p>Lengkapi seluruh kolom bertanda wajib.</p>
                    </div>
                </div>

                <?php if ($pesan !== ""): ?>
                    <div class="add-alert" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= e($pesan) ?>
                    </div>
                <?php endif; ?>

                <form action="add.php" method="POST">

                    <div class="add-form-grid">

                        <div class="add-field">
                            <label for="nama_siswa">
                                Nama Siswa <span>*</span>
                            </label>

                            <div class="add-input-wrap">
                                <i class="bi bi-person"></i>

                                <input
                                    type="text"
                                    id="nama_siswa"
                                    name="nama_siswa"
                                    maxlength="100"
                                    placeholder="Masukkan nama siswa"
                                    value="<?= e($nama_siswa) ?>"
                                    autocomplete="name"
                                    required
                                >
                            </div>
                        </div>

                        <div class="add-field">
                            <label for="kelas">
                                Kelas <span>*</span>
                            </label>

                            <div class="add-input-wrap">
                                <i class="bi bi-mortarboard"></i>

                                <select name="kelas" id="kelas" required>
                                    <option value="">-- Pilih Kelas --</option>

                                    <?php foreach ([
                                        "X PPLG", "XI PPLG", "XII PPLG",
                                        "X TKJ", "XI TKJ", "XII TKJ",
                                        "X TO", "XI TO", "XII TO",
                                        "X DKV", "XI DKV", "XII DKV",
                                        "X MPLB", "XI MPLB", "XII MPLB"
                                    ] as $item): ?>
                                        <option
                                            value="<?= e($item) ?>"
                                            <?= $kelas === $item ? "selected" : "" ?>
                                        >
                                            <?= e($item) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="add-field add-field-full">
                            <label for="fasilitas">
                                Fasilitas yang Rusak <span>*</span>
                            </label>

                            <div class="add-input-wrap">
                                <i class="bi bi-tools"></i>

                                <select
                                    id="fasilitas"
                                    name="fasilitas"
                                    required
                                >
                                    <option value="">-- Pilih Fasilitas --</option>

                                    <?php foreach ([
                                        "Meja", "Kursi", "Papan Tulis",
                                        "Lampu", "Pendingin Udara",
                                        "Pintu", "Jendela", "Infocus",
                                        "Komputer"
                                    ] as $item): ?>
                                        <option
                                            value="<?= e($item) ?>"
                                            <?= $fasilitas === $item ? "selected" : "" ?>
                                        >
                                            <?= e($item) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="add-field add-field-full">
                            <label for="keterangan">
                                Keterangan Pengaduan <span>*</span>
                            </label>

                            <textarea
                                id="keterangan"
                                name="keterangan"
                                rows="5"
                                maxlength="3000"
                                placeholder="Jelaskan kondisi kerusakan, lokasi, dan informasi lainnya..."
                                required
                            ><?= e($keterangan) ?></textarea>

                            <small class="add-help">
                                Jelaskan kerusakan secara singkat dan jelas.
                            </small>
                        </div>

                    </div>

                    <div class="add-form-footer">
                        <p>
                            <i class="bi bi-info-circle"></i>
                            Pastikan informasi sudah benar sebelum dikirim.
                        </p>

                        <div class="add-actions">
                            <a href="index.php" class="add-btn add-btn-cancel">
                                <i class="bi bi-arrow-left"></i>
                                Kembali
                            </a>

                            <button type="submit" class="add-btn add-btn-submit">
                                <i class="bi bi-send-fill"></i>
                                Kirim Pengaduan
                            </button>
                        </div>
                    </div>

                </form>
            </section>

            <footer class="add-footer">
                &copy; <?= date("Y") ?> Sistem Pengaduan Fasilitas Sekolah
            </footer>

        </div>

    </main>

    <?php require "sidebar.php"; ?>

</div>

</body>
</html>
