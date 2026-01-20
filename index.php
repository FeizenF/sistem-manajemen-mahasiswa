<?php
session_start();
require_once 'db_connect.php';

$total_mahasiswa = $pdo->query("SELECT COUNT(*) FROM Tb_Mahasiswa")->fetchColumn();
$total_jurusan = $pdo->query("SELECT COUNT(*) FROM Tb_Jurusan")->fetchColumn();
$total_dosen = $pdo->query("SELECT COUNT(*) FROM Tb_Dosen")->fetchColumn();

$mahasiswa_terbaru = $pdo->query("
    SELECT m.NIM, m.Nama_Mhs, j.Nama_Jurusan, d.Nama_Dosen
    FROM Tb_Mahasiswa m
    INNER JOIN Tb_Jurusan j ON m.Id_jurusan_fk = j.id_jurusan
    INNER JOIN Tb_Dosen d ON m.Id_Dosenwali_fk = d.id_Dosen
    ORDER BY m.NIM DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Mahasiswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f9fafb; padding: 20px; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* Navigation */
        .navbar { background: #2c3e50; padding: 18px 25px; border-radius: 10px; margin-bottom: 30px; }
        .nav-links { display: flex; gap: 10px; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .nav-link.active { background: #3498db; color: white; font-weight: 600; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 40px; padding: 40px 20px; background: white; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
        .header h1 { color: #2c3e50; margin-bottom: 12px; font-size: 32px; font-weight: 700; }
        .header p { color: #7f8c8d; font-size: 16px; max-width: 600px; margin: 0 auto; line-height: 1.6; }
        
        /* Stats */
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 40px; }
        @media (max-width: 768px) { .stats { grid-template-columns: 1fr; } }
        .stat-card { background: white; padding: 28px; text-align: center; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); transition: transform 0.3s; border-left: 4px solid #3498db; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-number { font-size: 38px; font-weight: 700; color: #2c3e50; margin-bottom: 8px; }
        .stat-label { color: #7f8c8d; font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Content */
        .content { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        @media (max-width: 768px) { .content { grid-template-columns: 1fr; } }
        
        /* Panel */
        .panel { background: white; padding: 28px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
        .panel h2 { color: #2c3e50; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #ecf0f1; font-size: 20px; font-weight: 600; }
        
        /* Table */
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th { text-align: left; padding: 16px 20px; background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #495057; font-weight: 600; font-size: 14px; }
        .table td { padding: 16px 20px; border-bottom: 1px solid #ecf0f1; color: #34495e; font-size: 15px; }
        .table tr:hover { background: #f8f9fa; }
        
        /* Badge */
        .badge { display: inline-block; padding: 6px 14px; background: #3498db; color: white; border-radius: 4px; font-size: 13px; font-weight: 600; }
        
        /* Quick Actions */
        .quick-actions { display: flex; flex-direction: column; gap: 16px; }
        .action-btn { display: flex; align-items: center; gap: 15px; padding: 18px; background: #f8f9fa; text-decoration: none; color: #2c3e50; border-radius: 8px; transition: all 0.3s; border: none; cursor: pointer; width: 100%; text-align: left; }
        .action-btn:hover { background: #3498db; color: white; transform: translateX(5px); }
        .action-icon { width: 40px; height: 40px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; color: #495057; }
        .action-btn:hover .action-icon { background: rgba(255,255,255,0.2); color: white; }
        .action-text { flex: 1; }
        .action-text strong { display: block; font-size: 16px; margin-bottom: 4px; }
        .action-text span { font-size: 14px; color: #7f8c8d; }
        .action-btn:hover .action-text span { color: rgba(255,255,255,0.9); }
        
        /* Button */
        .btn { display: inline-block; padding: 14px 28px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; transition: all 0.3s; border: none; cursor: pointer; }
        .btn:hover { background: #2980b9; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3); }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 40px 20px; color: #95a5a6; }
        .empty-state p { margin-bottom: 24px; font-size: 16px; }
        
        /* Footer */
        .footer { margin-top: 40px; text-align: center; color: #95a5a6; font-size: 14px; padding: 20px; }
        
        /* System Info */
        .system-info { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        .system-info p { color: #7f8c8d; font-size: 14px; margin-bottom: 10px; }
        .system-info strong { color: #2c3e50; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="index.php" class="nav-link active">Dashboard</a>
                <a href="form_mahasiswa.php" class="nav-link">Input Data</a>
                <a href="daftar_mahasiswa.php" class="nav-link">Data Mahasiswa</a>
            </div>
        </nav>
        
        <div class="header">
            <h1>Sistem Data Mahasiswa</h1>
            <p>Kelola dan monitor data akademik dengan sistem terintegrasi</p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_mahasiswa; ?></div>
                <div class="stat-label">Total Mahasiswa</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_jurusan; ?></div>
                <div class="stat-label">Program Studi</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_dosen; ?></div>
                <div class="stat-label">Dosen Wali</div>
            </div>
        </div>
        
        <div class="content">
            <div class="panel">
                <h2>Mahasiswa Terbaru</h2>
                <?php if (count($mahasiswa_terbaru) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Dosen Wali</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswa_terbaru as $mhs): ?>
                            <tr>
                                <td><span class="badge"><?php echo htmlspecialchars($mhs['NIM']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($mhs['Nama_Mhs']); ?></strong></td>
                                <td><?php echo htmlspecialchars($mhs['Nama_Jurusan']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['Nama_Dosen']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 25px;">
                        <a href="daftar_mahasiswa.php" class="btn">Lihat Semua Data Mahasiswa</a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Belum ada data mahasiswa yang tersimpan</p>
                        <a href="form_mahasiswa.php" class="btn" style="margin-top: 15px;">Tambah Data Pertama</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="panel">
                <h2>Aksi Cepat</h2>
                <div class="quick-actions">
                    <a href="form_mahasiswa.php" class="action-btn">
                        <div class="action-icon">+</div>
                        <div class="action-text">
                            <strong>Tambah Mahasiswa Baru</strong>
                            <span>Input data mahasiswa baru ke sistem</span>
                        </div>
                    </a>
                    
                    <a href="daftar_mahasiswa.php" class="action-btn">
                        <div class="action-icon">-</div>
                        <div class="action-text">
                            <strong>Lihat Data Mahasiswa</strong>
                            <span>Tampilkan semua data yang tersimpan</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Sistem Data Mahasiswa &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</body>
</html>