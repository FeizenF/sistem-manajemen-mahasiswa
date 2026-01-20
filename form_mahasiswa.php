<?php
session_start();
require_once 'db_connect.php';

$jurusan = $pdo->query("SELECT * FROM Tb_Jurusan ORDER BY Nama_Jurusan")->fetchAll();
$dosen = $pdo->query("SELECT * FROM Tb_Dosen ORDER BY Nama_Dosen")->fetchAll();

$old_nim = $_SESSION['old_nim'] ?? '';
$old_nama = $_SESSION['old_nama'] ?? '';
$old_email = $_SESSION['old_email'] ?? '';
$old_jurusan = $_SESSION['old_jurusan'] ?? '';
$old_dosen = $_SESSION['old_dosen'] ?? '';

$errors = $_SESSION['errors'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Mahasiswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: #f9fafb; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
        
        .navbar { background: #2c3e50; padding: 15px 25px; border-radius: 10px 10px 0 0; }
        .nav-links { display: flex; gap: 10px; }
        .nav-link { color: rgba(255,255,255,0.9); text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .nav-link.active { background: #3498db; color: white; font-weight: 600; }
        
        .form-container { padding: 30px; }
        .form-container h2 { color: #2c3e50; margin-bottom: 25px; font-size: 24px; font-weight: 600; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; }
        input, select { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px; color: #2c3e50; }
        input:focus, select:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        
        .btn { display: block; width: 100%; padding: 14px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn:hover { background: #2980b9; }
        
        .message { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .error-list { margin: 10px 0; padding-left: 20px; }
        .error-list li { margin-bottom: 5px; }
        
        .field-error { border-color: #ef4444 !important; }
        
        .form-links { margin-top: 25px; text-align: center; }
        .form-links a { color: #3498db; text-decoration: none; }
        .form-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="form_mahasiswa.php" class="nav-link active">Input Data</a>
                <a href="daftar_mahasiswa.php" class="nav-link">Data Mahasiswa</a>
            </div>
        </nav>
        
        <div class="form-container">
            <h2>Input Data Mahasiswa Baru</h2>
            
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="message success">Data berhasil disimpan!</div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="message error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="proses_input.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nim">NIM *</label>
                        <input type="text" id="nim" name="nim" required maxlength="10" 
                               placeholder="10 digit angka" value="<?php echo htmlspecialchars($old_nim); ?>"
                               class="<?php echo (in_array('NIM harus 10 digit angka', $errors) || in_array('NIM tidak boleh kosong', $errors)) ? 'field-error' : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="nama">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" required 
                               placeholder="Nama mahasiswa" value="<?php echo htmlspecialchars($old_nama); ?>"
                               class="<?php echo in_array('Nama tidak boleh kosong', $errors) ? 'field-error' : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="email@example.com" value="<?php echo htmlspecialchars($old_email); ?>"
                           class="<?php echo in_array('Email tidak valid', $errors) ? 'field-error' : ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="jurusan">Jurusan *</label>
                        <select id="jurusan" name="jurusan" required
                                class="<?php echo in_array('Jurusan harus dipilih', $errors) ? 'field-error' : ''; ?>">
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach ($jurusan as $j): ?>
                            <option value="<?php echo $j['id_jurusan']; ?>" 
                                <?php echo ($old_jurusan == $j['id_jurusan']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($j['Nama_Jurusan']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="dosen_wali">Dosen Wali *</label>
                        <select id="dosen_wali" name="dosen_wali" required
                                class="<?php echo in_array('Dosen wali harus dipilih', $errors) ? 'field-error' : ''; ?>">
                            <option value="">-- Pilih Dosen --</option>
                            <?php foreach ($dosen as $d): ?>
                            <option value="<?php echo $d['id_Dosen']; ?>"
                                <?php echo ($old_dosen == $d['id_Dosen']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d['Nama_Dosen']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn">Simpan Data</button>
            </form>
            
            <div class="form-links">
                <a href="daftar_mahasiswa.php">Lihat data mahasiswa →</a>
            </div>
        </div>
    </div>
    
    <?php

    unset($_SESSION['errors']);
    unset($_SESSION['old_nim']);
    unset($_SESSION['old_nama']);
    unset($_SESSION['old_email']);
    unset($_SESSION['old_jurusan']);
    unset($_SESSION['old_dosen']);

    ?>
</body>
</html>

