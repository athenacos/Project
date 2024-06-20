<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "dbPegawai"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Employee</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <form method="POST">
        <label for="nip">NIP:</label>
        <input 
            type="text" 
            id="nip" 
            name="nip" 
            required 
            placeholder="Masukkan nip anda">
        <input type="submit" value="Search">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nip = $_POST['nip'];

        $sql = "SELECT * FROM TblPegawai WHERE NIP = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $nip);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<form method="post" action="export.php">
                    <input type="hidden" name="nip" value="' . $nip . '">
                    <input type="submit" name="export_excel" value="Export to Excel">
                    <input type="submit" name="export_pdf" value="Export to PDF">
                  </form>';
            
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<p>NIP: " . $row["NIP"] . "</p>";
                echo "<p>Nama: " . $row["Nama"] . "</p>";
                echo "<p>Alamat: " . $row["Alamat"] . "</p>";
                echo "<p>Tanggal Lahir: " . $row["Tanggal_lahir"] . "</p>";
                echo "<p>Kode Divisi: " . $row["Kode_Divisi"] . "</p>";
            }
        } else {
            echo "<p>No employee found with NIP: $nip</p>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>
</body>
</html>
