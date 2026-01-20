<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $id_jurusan = $_POST['jurusan'] ?? '';
    $id_dosen_wali = $_POST['dosen_wali'] ?? '';
    
    $_SESSION['old_nim'] = $nim;
    $_SESSION['old_nama'] = $nama;
    $_SESSION['old_email'] = $email;
    $_SESSION['old_jurusan'] = $id_jurusan;
    $_SESSION['old_dosen'] = $id_dosen_wali;
    
    // Validasi
    $errors = [];
    
    if (empty($nim)) {
        $errors[] = "NIM tidak boleh kosong";
    } elseif (!preg_match('/^[0-9]{10}$/', $nim)) {
        $errors[] = "NIM harus 10 digit angka";
    }
    
    if (empty($nama)) {
        $errors[] = "Nama tidak boleh kosong";
    }
    
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid (contoh: nama@domain.com)";
    }
    
    if (empty($id_jurusan) || !is_numeric($id_jurusan)) {
        $errors[] = "Jurusan harus dipilih";
    }
    
    if (empty($id_dosen_wali) || !is_numeric($id_dosen_wali)) {
        $errors[] = "Dosen wali harus dipilih";
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: form_mahasiswa.php');
        exit;
    }
    
    try {
        $sql = "INSERT INTO Tb_Mahasiswa (NIM, Nama_Mhs, email, Id_jurusan_fk, Id_Dosenwali_fk) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nim, $nama, $email, $id_jurusan, $id_dosen_wali]);
        
        unset($_SESSION['old_nim']);
        unset($_SESSION['old_nama']);
        unset($_SESSION['old_email']);
        unset($_SESSION['old_jurusan']);
        unset($_SESSION['old_dosen']);
        
        header('Location: form_mahasiswa.php?status=success');
        exit;
        
    } catch (PDOException $e) {
        $message = $e->getCode() == 23000 ? 
            "NIM '$nim' sudah terdaftar" : "Terjadi kesalahan pada database";
        
        $errors[] = $message;
        $_SESSION['errors'] = $errors;
        header('Location: form_mahasiswa.php');
        exit;
    }
} else {
    header('Location: form_mahasiswa.php');
    exit;
}
?>