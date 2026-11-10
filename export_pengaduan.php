
<?php
require "auth.php";
wajibLogin();
require "koneksi.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$userId = (int) ($_SESSION["user_id"] ?? 0);

// Query berdasarkan hak akses.
if ($isAdmin) {
    $sql = "SELECT p.*, u.nama AS nama_pelapor
            FROM pengaduan p
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.id DESC";

    $result = mysqli_query($conn, $sql);
} else {
    $sql = "SELECT p.*, u.nama AS nama_pelapor
            FROM pengaduan p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
            ORDER BY p.id DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        http_response_code(500);
        exit("Gagal menyiapkan query pengaduan.");
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

if (!$result) {
    http_response_code(500);
    exit("Gagal mengambil data pengaduan.");
}

// Buat workbook Excel.
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Data Pengaduan");

// Header kolom.
$headers = ["No."];

if ($isAdmin) {
    $headers[] = "Pelapor";
}

$headers = array_merge($headers, [
    "Nama Siswa",
    "Kelas",
    "Fasilitas",
    "Keterangan",
    "Tanggal"
]);

$lastColumnIndex = count($headers);
$lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
    $lastColumnIndex
);

// Tulis header.
foreach ($headers as $index => $header) {
    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
        $index + 1
    );

    $sheet->setCellValue($column . "1", $header);
}

// Gaya header hijau.
$sheet->getStyle("A1:" . $lastColumn . "1")->applyFromArray([
    "font" => [
        "bold" => true,
        "color" => ["rgb" => "FFFFFF"],
        "size" => 11
    ],
    "fill" => [
        "fillType" => Fill::FILL_SOLID,
        "startColor" => ["rgb" => "176B45"]
    ],
    "alignment" => [
        "horizontal" => "center",
        "vertical" => "center",
        "wrapText" => true
    ],
    "borders" => [
        "allBorders" => [
            "borderStyle" => Border::BORDER_THIN,
            "color" => ["rgb" => "B7C9BE"]
        ]
    ]
]);

$sheet->getRowDimension(1)->setRowHeight(25);

// Isi data.
$rowNumber = 2;
$no = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $data = [$no++];

    if ($isAdmin) {
        $data[] = $row["nama_pelapor"] ?? "-";
    }

    $data[] = $row["nama_siswa"] ?? "-";
    $data[] = $row["kelas"] ?? "-";
    $data[] = $row["fasilitas"] ?? "-";
    $data[] = $row["keterangan"] ?? "-";
    $data[] = $row["tanggal"] ?? "-";

    foreach ($data as $index => $value) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
            $index + 1
        );

        // Tulis sebagai teks agar data tidak dianggap formula Excel.
        // Nomor urut tetap berupa angka.
        if ($index === 0) {
            $sheet->setCellValueExplicit(
                $column . $rowNumber,
                (int) $value,
                DataType::TYPE_NUMERIC
            );
        } else {
            $sheet->setCellValueExplicit(
                $column . $rowNumber,
                (string) $value,
                DataType::TYPE_STRING
            );
        }
    }

    // Warna baris selang-seling.
    if ($rowNumber % 2 === 0) {
        $sheet->getStyle(
            "A" . $rowNumber . ":" . $lastColumn . $rowNumber
        )->applyFromArray([
            "fill" => [
                "fillType" => Fill::FILL_SOLID,
                "startColor" => ["rgb" => "E8F3EC"]
            ]
        ]);
    }

    $rowNumber++;
}

$lastRow = $rowNumber - 1;

// Border seluruh tabel dan perataan isi.
if ($lastRow >= 1) {
    $sheet->getStyle("A1:" . $lastColumn . $lastRow)
        ->applyFromArray([
            "borders" => [
                "allBorders" => [
                    "borderStyle" => Border::BORDER_THIN,
                    "color" => ["rgb" => "B7C9BE"]
                ]
            ],
            "alignment" => [
                "vertical" => "top"
            ]
        ]);

    // Aktifkan filter kolom.
    $sheet->setAutoFilter("A1:" . $lastColumn . $lastRow);
}

// Lebar kolom otomatis.
for ($i = 1; $i <= $lastColumnIndex; $i++) {
    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
        $i
    );

    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Batasi lebar kolom keterangan agar tidak terlalu lebar.
$keteranganIndex = $isAdmin ? "F" : "E";
$sheet->getColumnDimension($keteranganIndex)->setAutoSize(false);
$sheet->getColumnDimension($keteranganIndex)->setWidth(40);
$sheet->getStyle(
    $keteranganIndex . "2:" . $keteranganIndex . max(2, $lastRow)
)->getAlignment()->setWrapText(true);

// Bekukan header agar tetap terlihat saat scroll.
$sheet->freezePane("A2");

// Nama file download.
$filename = "data_pengaduan_" . date("Y-m-d_H-i-s") . ".xlsx";

// Bersihkan buffer output agar file Excel tidak rusak.
while (ob_get_level() > 0) {
    ob_end_clean();
}

header(
    "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
);
header(
    'Content-Disposition: attachment; filename="' . $filename . '"'
);
header("Cache-Control: max-age=0");
header("Pragma: public");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

$spreadsheet->disconnectWorksheets();
exit;
