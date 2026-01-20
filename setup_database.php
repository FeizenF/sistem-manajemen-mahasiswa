<?php
// setup_database.php
$host = 'localhost';
$username = 'user';
$password = 'user';

try {
    // Koneksi ke MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Setup Database Sistem Mahasiswa</h2>";
    
    // 1. Buat database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS db_kampus");
    echo "<p>Database 'db_kampus' dibuat</p>";
    
    $pdo->exec("USE db_kampus");
    
    // 2. Buat tabel Tb_Jurusan
    $pdo->exec("DROP TABLE IF EXISTS Tb_Jurusan");
    $pdo->exec("CREATE TABLE Tb_Jurusan (
        id_jurusan INT AUTO_INCREMENT PRIMARY KEY,
        Nama_Jurusan VARCHAR(30) NOT NULL UNIQUE
    )");
    echo "<p>Tabel 'Tb_Jurusan' dibuat</p>";
    
    // 3. Buat tabel Tb_Dosen
    $pdo->exec("DROP TABLE IF EXISTS Tb_Dosen");
    $pdo->exec("CREATE TABLE Tb_Dosen (
        id_Dosen INT AUTO_INCREMENT PRIMARY KEY,
        Nama_Dosen VARCHAR(30) NOT NULL UNIQUE
    )");
    echo "<p>Tabel 'Tb_Dosen' dibuat</p>";
    
    // 4. Buat tabel Tb_Mahasiswa
    $pdo->exec("DROP TABLE IF EXISTS Tb_Mahasiswa");
    $pdo->exec("CREATE TABLE Tb_Mahasiswa (
        NIM CHAR(10) PRIMARY KEY,
        Nama_Mhs VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL,
        Id_jurusan_fk INT NOT NULL,
        Id_Dosenwali_fk INT NOT NULL,
        FOREIGN KEY (Id_jurusan_fk) REFERENCES Tb_Jurusan(id_jurusan),
        FOREIGN KEY (Id_Dosenwali_fk) REFERENCES Tb_Dosen(id_Dosen)
    )");
    echo "<p>Tabel 'Tb_Mahasiswa' dibuat</p>";
    
    // 5. Insert data jurusan
    $jurusan_data = [
        'Informatika',
        'Sistem Informasi', 
        'Teknik Komputer',
        'Informatika Medis',
        'Psikologi',
        'Managemen'
    ];
    
    foreach ($jurusan_data as $nama) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Tb_Jurusan WHERE Nama_Jurusan = ?");
        $stmt->execute([$nama]);
        if ($stmt->fetch()['count'] == 0) {
            $pdo->prepare("INSERT INTO Tb_Jurusan (Nama_Jurusan) VALUES (?)")->execute([$nama]);
        }
    }
    echo "<p>Data jurusan dimasukkan</p>";
    
    // 6. Insert data dosen
    $dosen_data = [
        'Dosen A',
        'Dosen B',
        'Dosen C',
        'Dosen D',
        'Dosen E',
    ];
    
    foreach ($dosen_data as $nama) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Tb_Dosen WHERE Nama_Dosen = ?");
        $stmt->execute([$nama]);
        if ($stmt->fetch()['count'] == 0) {
            $pdo->prepare("INSERT INTO Tb_Dosen (Nama_Dosen) VALUES (?)")->execute([$nama]);
        }
    }
    echo "<p>Data dosen dimasukkan</p>";
    
    echo "<hr><h3>Setup Berhasil!</h3>";
    echo "<p><a href='form_mahasiswa.php'>Input Data Mahasiswa</a></p>";
    echo "<p><a href='daftar_mahasiswa.php'>Lihat Data Mahasiswa</a></p>";
    
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>