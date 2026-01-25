<?php
// config/session.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isPenjual() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'penjual';
}

function isPembeli() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'pembeli';
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserName() {
    return $_SESSION['nama'] ?? 'Guest';
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}
?>