<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS absensi_gaji');
    echo 'Database absensi_gaji created or already exists.';
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
