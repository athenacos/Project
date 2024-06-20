<?php
require 'vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "dbPegawai"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nip = $_POST['nip'];

if (isset($_POST["export_excel"])) {
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sql = "SELECT * FROM TblPegawai WHERE NIP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nip);
    $stmt->execute();
    $result = $stmt->get_result();

    $rowNumber = 1;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['NIP']);
        $sheet->setCellValue('B' . $rowNumber, $row['Nama']);
        $sheet->setCellValue('C' . $rowNumber, $row['Alamat']);
        $sheet->setCellValue('D' . $rowNumber, $row['Tanggal_lahir']);
        $sheet->setCellValue('E' . $rowNumber, $row['Kode_Divisi']);
        $rowNumber++;
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="employee.xlsx"');
    $writer->save('php://output');
    exit;
}

if (isset($_POST["export_pdf"])) {
    use TCPDF;

    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Employee Details</h1>';

    $sql = "SELECT * FROM TblPegawai WHERE NIP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nip);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $html .= "<p>NIP: " . $row['NIP'] . "</p>";
        $html .= "<p>Nama: " . $row['Nama'] . "</p>";
        $html .= "<p>Alamat: " . $row['Alamat'] . "</p>";
        $html .= "<p>Tanggal Lahir: " . $row['Tanggal_lahir'] . "</p>";
        $html .= "<p>Kode Divisi: " . $row['Kode_Divisi'] . "</p>";
    }

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('employee.pdf', 'D');
    exit;
}

$conn->close();
?>
