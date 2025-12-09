<?php
/**
 * File: mahasiswa/konfirmasi_revisi.php
 * Mahasiswa mengupload file revisi dan konfirmasi revisi sudah dilakukan
 */

session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_mahasiswa();

$mahasiswa = get_mahasiswa_data($conn, $_SESSION['user_id']);
$id_mahasiswa = $mahasiswa['id_mahasiswa'];

// Handle upload revisi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_revisi'])) {
    $id_registrasi = intval($_POST['id_registrasi']);
    $id_penilaian = intval($_POST['id_penilaian']);
    $catatan_mahasiswa = clean_input($_POST['catatan_mahasiswa']);
    
    // Upload file revisi
    $upload_dir = '../uploads/revisi/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $file_revisi = '';
    if (isset($_FILES['file_revisi']) && $_FILES['file_revisi']['error'] == 0) {
        $file_ext = pathinfo($_FILES['file_revisi']['name'], PATHINFO_EXTENSION);
        $file_revisi = 'revisi_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        move_uploaded_file($_FILES['file_revisi']['tmp_name'], $upload_dir . $file_revisi);
    }
    
    // Insert ke tabel revisi
    $sql = "INSERT INTO revisi_disertasi (id_registrasi, id_penilaian, catatan_revisi, file_revisi, status, tanggal_kirim) 
            VALUES (?, ?, ?, ?, 'dikirim', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $id_registrasi, $id_penilaian, $catatan_mahasiswa, $file_revisi);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Revisi berhasil dikirim!";
    } else {
        $_SESSION['error_message'] = "Gagal mengirim revisi: " . $stmt->error;
    }
    
    header("Location: konfirmasi_revisi.php");
    exit();
}

// Ambil data penilaian yang perlu direvisi
$sql = "SELECT p.*, r.jenis_ujian, r.judul_disertasi, d.nama_lengkap as penilai,
               (SELECT COUNT(*) FROM revisi_disertasi rd WHERE rd.id_penilaian = p.id_penilaian) as jumlah_revisi
        FROM penilaian_ujian p
        JOIN registrasi r ON p.id_registrasi = r.id_registrasi
        JOIN dosen d ON p.id_dosen = d.id_dosen
        WHERE r.id_mahasiswa = ? AND p.catatan IS NOT NULL AND p.catatan != ''
        ORDER BY p.tanggal_penilaian DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Konfirmasi Revisi - Sistem Disertasi S3 UKSW";
include '../includes/header.php';
include '../includes/sidebar_mahasiswa.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📝 Konfirmasi Revisi Disertasi</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($penilaian = $result->fetch_assoc()): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><?= ucfirst($penilaian['jenis_ujian']); ?> - Dinilai oleh: <?= $penilaian['penilai']; ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Catatan Revisi dari Dosen:</h6>
                                            <div class="alert alert-warning">
                                                <?= nl2br(htmlspecialchars($penilaian['catatan'])); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Form Konfirmasi Revisi:</h6>
                                            <form method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="id_registrasi" value="<?= $penilaian['id_registrasi']; ?>">
                                                <input type="hidden" name="id_penilaian" value="<?= $penilaian['id_penilaian']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">File Revisi (PDF/Doc)</label>
                                                    <input type="file" name="file_revisi" class="form-control" accept=".pdf,.doc,.docx">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan untuk Dosen</label>
                                                    <textarea name="catatan_mahasiswa" class="form-control" rows="3" placeholder="Jelaskan revisi yang telah dilakukan..."></textarea>
                                                </div>
                                                
                                                <button type="submit" name="submit_revisi" class="btn btn-success">
                                                    ✅ Konfirmasi Revisi Selesai
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <?php if ($penilaian['jumlah_revisi'] > 0): ?>
                                    <div class="mt-3">
                                        <h6>Riwayat Revisi:</h6>
                                        <!-- Tampilkan riwayat revisi sebelumnya -->
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Tidak ada revisi yang perlu dikonfirmasi.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>