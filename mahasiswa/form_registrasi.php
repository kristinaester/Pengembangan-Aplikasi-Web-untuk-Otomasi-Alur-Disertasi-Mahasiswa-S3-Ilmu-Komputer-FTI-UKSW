<?php
/**
 * File: mahasiswa/form_registrasi.php
 * Form registrasi ujian multi-step (3 tahap)
 */

session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

require_mahasiswa();

$mahasiswa = get_mahasiswa_data($conn, $_SESSION['user_id']);
$jenis_ujian = isset($_GET['jenis']) ? $_GET['jenis'] : 'proposal';
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

// Validasi jenis ujian
$valid_jenis = ['proposal', 'kualifikasi', 'kelayakan', 'tertutup'];
if (!in_array($jenis_ujian, $valid_jenis)) {
    header("Location: registrasi.php");
    exit();
}

// Nama jenis ujian
$nama_ujian = [
    'proposal' => 'Ujian Proposal',
    'kualifikasi' => 'Ujian Kualifikasi',
    'kelayakan' => 'Ujian Kelayakan',
    'tertutup' => 'Ujian Tertutup'
];

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_final'])) {
    $judul_disertasi = clean_input($_POST['judul_disertasi']);
    $promotor = clean_input($_POST['promotor']);
    $co_promotor = clean_input($_POST['co_promotor']);
    
    // Insert data registrasi
    $query = "INSERT INTO registrasi (id_mahasiswa, jenis_ujian, judul_disertasi, tanggal_pengajuan, status) 
              VALUES (" . $mahasiswa['id_mahasiswa'] . ", '$jenis_ujian', '" . escape_string($judul_disertasi) . "', NOW(), 'Menunggu')";
    
    if (mysqli_query($conn, $query)) {
        $id_registrasi = mysqli_insert_id($conn);
        
        // Upload files
        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            foreach ($_FILES['files']['name'] as $key => $filename) {
                if ($_FILES['files']['error'][$key] == 0) {
                    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = time() . '_' . $key . '.' . $file_ext;
                    $target_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target_path)) {
                        $query_file = "INSERT INTO lampiran (id_registrasi, nama_berkas, path_berkas) 
                                      VALUES ($id_registrasi, '" . escape_string($filename) . "', '" . escape_string($new_filename) . "')";
                        mysqli_query($conn, $query_file);
                    }
                }
            }
        }
        
        $_SESSION['success_message'] = "Registrasi berhasil! Mohon menunggu verifikasi dari admin.";
        header("Location: dashboard.php");
        exit();
    }
}

$page_title = "Pendaftaran " . $nama_ujian[$jenis_ujian] . " - Sistem Disertasi S3 UKSW";
include '../includes/header.php';
include '../includes/sidebar_mahasiswa.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0" style="background: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200') center/cover; min-height: 200px; position: relative;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(13, 110, 253, 0.85);"></div>
                    <div class="card-body text-white d-flex align-items-center" style="position: relative; z-index: 1;">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb text-white mb-2">
                                    <li class="breadcrumb-item"><a href="dashboard.php" class="text-white">Beranda</a></li>
                                    <li class="breadcrumb-item"><a href="registrasi.php" class="text-white">Registrasi</a></li>
                                    <li class="breadcrumb-item active text-white"><?php echo $nama_ujian[$jenis_ujian]; ?></li>
                                </ol>
                            </nav>
                            <h2 class="mb-2">Pendaftaran <?php echo $nama_ujian[$jenis_ujian]; ?></h2>
                            <p class="mb-0">Registrasi > <?php echo $nama_ujian[$jenis_ujian]; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="row">
            <div class="col-md-9 mx-auto">
                <div class="card border-0">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Pendaftaran <?php echo $nama_ujian[$jenis_ujian]; ?></h5>
                        
                        <!-- Progress Steps -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between position-relative">
                                <div style="position: absolute; top: 20px; left: 0; right: 0; height: 2px; background: #e9ecef; z-index: 0;"></div>
                                
                                <div class="text-center position-relative" style="z-index: 1;">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?php echo $step >= 1 ? 'bg-primary text-white' : 'bg-light text-muted'; ?>" style="width: 40px; height: 40px; border: 2px solid <?php echo $step >= 1 ? '#0d6efd' : '#dee2e6'; ?>;">
                                        <strong>1</strong>
                                    </div>
                                    <div class="small mt-2">Identitas Mahasiswa</div>
                                </div>
                                
                                <div class="text-center position-relative" style="z-index: 1;">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?php echo $step >= 2 ? 'bg-primary text-white' : 'bg-light text-muted'; ?>" style="width: 40px; height: 40px; border: 2px solid <?php echo $step >= 2 ? '#0d6efd' : '#dee2e6'; ?>;">
                                        <strong>2</strong>
                                    </div>
                                    <div class="small mt-2">Informasi Disertasi</div>
                                </div>
                                
                                <div class="text-center position-relative" style="z-index: 1;">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?php echo $step >= 3 ? 'bg-primary text-white' : 'bg-light text-muted'; ?>" style="width: 40px; height: 40px; border: 2px solid <?php echo $step >= 3 ? '#0d6efd' : '#dee2e6'; ?>;">
                                        <strong>3</strong>
                                    </div>
                                    <div class="small mt-2">Lampiran Persyaratan</div>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" enctype="multipart/form-data" id="registrationForm">
                            <?php if ($step == 1): ?>
                            <!-- Step 1: Identitas Mahasiswa -->
                            <div class="border border-primary rounded p-4 mb-4">
                                <h6 class="text-primary mb-3">A. Identitas Mahasiswa</h6>
                                <p class="small text-muted mb-4">Please fill out all the required section</p>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="<?php echo $mahasiswa['nama_lengkap']; ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="<?php echo $mahasiswa['nim']; ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="jenis_kelamin" value="Perempuan" <?php echo $mahasiswa['jenis_kelamin'] == 'Perempuan' ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Perempuan</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="jenis_kelamin" value="Laki-laki" <?php echo $mahasiswa['jenis_kelamin'] == 'Laki-laki' ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Laki-laki</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. Telp <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="<?php echo $mahasiswa['no_telp']; ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="<?php echo $mahasiswa['angkatan']; ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="registrasi.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <a href="?jenis=<?php echo $jenis_ujian; ?>&step=2" class="btn btn-success">
                                    Berikut<i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                            
                            <?php elseif ($step == 2): ?>
                            <!-- Step 2: Informasi Disertasi -->
                            <div class="border border-primary rounded p-4 mb-4">
                                <h6 class="text-primary mb-3">B. Informasi Disertasi</h6>
                                <p class="small text-muted mb-4">Please fill out all the required section</p>
                                
                                <div class="mb-3">
                                    <label class="form-label">Judul Disertasi <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="judul_disertasi" rows="3" required placeholder="Integrated Customer Data Analysis (ICDA) Berbasis Deep-Learning untuk Optimalisasi Customer Relationship Management"></textarea>
                                    <small class="text-muted">Gunakan bahasa Indonesia atau Inggris</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Promotor <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="promotor" value="<?php echo $mahasiswa['pembimbing_1']; ?>" placeholder="Dosen 1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Co-Promotor</label>
                                        <input type="text" class="form-control" name="co_promotor" value="<?php echo $mahasiswa['pembimbing_2']; ?>" placeholder="Dosen 2">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="?jenis=<?php echo $jenis_ujian; ?>&step=1" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <a href="?jenis=<?php echo $jenis_ujian; ?>&step=3" class="btn btn-success">
                                    Berikut<i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                            
                            <?php elseif ($step == 3): ?>
                            <!-- Step 3: Lampiran Persyaratan -->
                            <div class="border border-primary rounded p-4 mb-4">
                                <h6 class="text-primary mb-3">C. Lampiran Persyaratan</h6>
                                <p class="small text-muted mb-4">Make sure to upload each file to this correct section</p>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Catatan:</strong> Upload file dalam format PDF dengan maksimal ukuran 5MB per file.
                                </div>
                                
                                <div id="file-uploads">
                                    <div class="mb-3 border rounded p-3">
                                        <label class="form-label">Surat Persetujuan Dosen Pembimbing (Asli)</label>
                                        <small class="text-muted d-block mb-2">2 berkas</small>
                                        <input type="file" class="form-control" name="files[]" accept=".pdf">
                                    </div>
                                    
                                    <div class="mb-3 border rounded p-3">
                                        <label class="form-label">Letter of Acceptance dan Bukti Pembayaran</label>
                                        <small class="text-muted d-block mb-2">+ artikel berbahasa Inggris di international Bereputasi, atau artikel Indonesia + calon artikel Internasional Bereputasi. Satu artikel harus Q1 dan 2 Q2-Q4)</small>
                                        <input type="file" class="form-control" name="files[]" accept=".pdf">
                                    </div>
                                    
                                    <div class="mb-3 border rounded p-3">
                                        <label class="form-label">Bukti Pembayaran SPP Semester / Lunas SPP Semester Akhir</label>
                                        <small class="text-muted d-block mb-2">Dengan keterangan)</small>
                                        <input type="file" class="form-control" name="files[]" accept=".pdf">
                                    </div>
                                    
                                    <div class="mb-3 border rounded p-3">
                                        <label class="form-label">Surat Keterangan Lunas Uang Kemahasiswaan/Kesejahteraan</label>
                                        <small class="text-muted d-block mb-2">(sertakan bukti bayar yang rinci)</small>
                                        <input type="file" class="form-control" name="files[]" accept=".pdf">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="?jenis=<?php echo $jenis_ujian; ?>&step=2" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" name="submit_final" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Kirim
                                </button>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>