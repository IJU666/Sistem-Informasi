<?php
// config/database.php
$host = '127.0.0.1'; // lebih aman di Windows
$port = '3304';
$dbname = 'ngajual';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "Koneksi database BERHASIL 🚀";
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
