<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ngajual');

// Fungsi koneksi database
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Fungsi untuk query dengan prepared statement
function query($sql, $params = [], $types = '') {
    $conn = connectDB();
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error prepare statement: " . $conn->error);
        }
        
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $conn->query($sql);
    }
    
    $conn->close();
    return $result;
}

// Fungsi untuk insert/update/delete
function execute($sql, $params = [], $types = '') {
    $conn = connectDB();
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        $stmt->close();
    } else {
        $success = $conn->query($sql);
    }
    
    $conn->close();
    return $success;
}

// Fungsi sanitasi input
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>