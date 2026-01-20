<?php
session_start();
require_once 'db_connect.php';

try {
    $sql = "SELECT m.NIM, m.Nama_Mhs, m.email, j.Nama_Jurusan, d.Nama_Dosen
            FROM Tb_Mahasiswa m
            INNER JOIN Tb_Jurusan j ON m.Id_jurusan_fk = j.id_jurusan
            INNER JOIN Tb_Dosen d ON m.Id_Dosenwali_fk = d.id_Dosen
            ORDER BY m.NIM";
    
    $stmt = $pdo->query($sql);
    $mahasiswa = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f9fafb; padding: 20px; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
        
        .navbar { background: #2c3e50; padding: 15px 25px; border-radius: 10px 10px 0 0; }
        .nav-links { display: flex; gap: 10px; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .nav-link.active { background: #3498db; color: white; font-weight: 600; }
        
        .header { padding: 40px 30px; text-align: center; }
        .header h1 { color: #2c3e50; margin-bottom: 12px; font-size: 32px; font-weight: 700; }
        .header p { color: #7f8c8d; font-size: 16px; max-width: 600px; margin: 0 auto; line-height: 1.6; }
        
        .content { padding: 0 30px 30px; }
        
        .actions { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .btn { padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s; }
        .btn:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
        
        .table-container { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 16px 20px; background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #495057; font-weight: 600; font-size: 14px; }
        .table td { padding: 16px 20px; border-bottom: 1px solid #ecf0f1; color: #34495e; font-size: 15px; }
        .table tr:hover { background: #f8f9fa; }
        
        .badge { display: inline-block; padding: 6px 12px; background: #3498db; color: white; border-radius: 4px; font-size: 12px; font-weight: 600; }
        
        .empty-state { text-align: center; padding: 50px 20px; color: #95a5a6; }
        .empty-state h3 { font-size: 20px; margin-bottom: 12px; color: #7f8c8d; }
        .empty-state p { margin-bottom: 20px; font-size: 15px; }
        
        .footer { margin-top: 30px; text-align: center; color: #95a5a6; font-size: 14px; padding: 20px; }
        
        @media (max-width: 768px) {
            .actions { flex-direction: column; gap: 15px; }
            .table th, .table td { padding: 12px 8px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="form_mahasiswa.php" class="nav-link">Input Data</a>
                <a href="daftar_mahasiswa.php" class="nav-link active">Data Mahasiswa</a>
            </div>
        </nav>
        
        <div class="header">
            <h1>Data Mahasiswa</h1>
            <p>Daftar lengkap mahasiswa dengan jurusan dan dosen wali</p>
        </div>
        
        <div class="content">
            <div class="actions">
                <a href="form_mahasiswa.php" class="btn">Tambah Mahasiswa</a>
                <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
            </div>
            
            <div class="table-container">
                <?php if (count($mahasiswa) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jurusan</th>
                                <th>Dosen Wali</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswa as $index => $mhs): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($mhs['NIM']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($mhs['Nama_Mhs']); ?></strong></td>
                                <td><?php echo htmlspecialchars($mhs['email']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['Nama_Jurusan']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['Nama_Dosen']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>Belum ada data mahasiswa</h3>
                        <p>Silakan tambah data mahasiswa terlebih dahulu</p>
                        <a href="form_mahasiswa.php" class="btn" style="margin-top: 15px;">Tambah Data</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="footer">
                <p>Total: <?php echo count($mahasiswa); ?> mahasiswa</p>
                <p>Sistem Data Mahasiswa &copy; <?php echo date('Y'); ?></p>
            </div>
        </div>
    </div>
</body>
</html>