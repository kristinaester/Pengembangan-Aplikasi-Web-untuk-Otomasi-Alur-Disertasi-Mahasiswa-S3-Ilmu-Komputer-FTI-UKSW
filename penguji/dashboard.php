<?php
/**
 * File: dosen/dashboard.php
 * Dashboard khusus untuk dosen - DENGAN HERO SECTION (ISI TETAP SAMA)
 */

session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

require_dosen();

$user_id = $_SESSION['user_id'];

// Ambil data dosen lengkap
$sql_dosen = "SELECT d.*, u.username 
              FROM dosen d 
              JOIN users u ON d.user_id = u.id 
              WHERE d.user_id = ?";
$stmt_dosen = $conn->prepare($sql_dosen);
$stmt_dosen->bind_param("i", $user_id);
$stmt_dosen->execute();
$result_dosen = $stmt_dosen->get_result();
$dosen = $result_dosen->fetch_assoc();

if (!$dosen) {
    die("Data dosen tidak ditemukan.");
}

$id_dosen = $dosen['id_dosen'];

// **STATISTIK SEDERHANA YANG PASTI BERHASIL**
$stats = [
    'menunggu_penilaian' => 0,
    'sudah_dinilai' => 0,
    'revisi_diajukan' => 0,
    'total_ujian' => 0,
    'sebagai_promotor' => 0,
    'sebagai_co_promotor' => 0,
    'sebagai_co_promotor2' => 0,
    'sebagai_penguji' => 0
];

// 1. Hitung total ujian yang ditugaskan (QUERY SEDERHANA)
$sql_total = "SELECT COUNT(DISTINCT r.id_registrasi) as total
              FROM registrasi r
              LEFT JOIN jadwal_ujian j ON r.id_registrasi = j.id_registrasi
              WHERE r.status = 'Diterima'
              AND (r.promotor = ? OR r.co_promotor = ? OR  r.co_promotor2 = ? OR
                   j.promotor = ? OR j.penguji_1 = ? OR j.penguji_2 = ? OR j.penguji_3 = ?)";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("iiiiiii", $id_dosen, $id_dosen, $id_dosen, $id_dosen, $id_dosen, $id_dosen, $id_dosen);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_data = $result_total->fetch_assoc();
$stats['total_ujian'] = $total_data['total'] ?? 0;

// 2. Hitung sudah dinilai
$sql_dinilai = "SELECT COUNT(DISTINCT p.id_penilaian) as total
                FROM penilaian_ujian p
                JOIN registrasi r ON p.id_registrasi = r.id_registrasi
                WHERE p.id_dosen = ? AND r.status = 'Diterima'";
$stmt_dinilai = $conn->prepare($sql_dinilai);
$stmt_dinilai->bind_param("i", $id_dosen);
$stmt_dinilai->execute();
$result_dinilai = $stmt_dinilai->get_result();
$dinilai_data = $result_dinilai->fetch_assoc();
$stats['sudah_dinilai'] = $dinilai_data['total'] ?? 0;

// 3. Hitung menunggu penilaian
$stats['menunggu_penilaian'] = $stats['total_ujian'] - $stats['sudah_dinilai'];

// 4. Hitung revisi diajukan
$sql_revisi = "SELECT COUNT(DISTINCT rev.id_revisi) as total
               FROM revisi_disertasi rev
               JOIN penilaian_ujian p ON rev.id_penilaian = p.id_penilaian
               WHERE p.id_dosen = ? AND rev.status IN ('dikirim', 'diajukan', 'menunggu')";
$stmt_revisi = $conn->prepare($sql_revisi);
$stmt_revisi->bind_param("i", $id_dosen);
$stmt_revisi->execute();
$result_revisi = $stmt_revisi->get_result();
$revisi_data = $result_revisi->fetch_assoc();
$stats['revisi_diajukan'] = $revisi_data['total'] ?? 0;

// 5. Hitung sebagai promotor
$sql_promotor = "SELECT COUNT(DISTINCT r.id_registrasi) as total
                 FROM registrasi r
                 WHERE r.status = 'Diterima' AND r.promotor = ?" ;
$stmt_promotor = $conn->prepare($sql_promotor);
$stmt_promotor->bind_param("i", $id_dosen);
$stmt_promotor->execute();
$result_promotor = $stmt_promotor->get_result();
$promotor_data = $result_promotor->fetch_assoc();
$stats['sebagai_promotor'] = $promotor_data['total'] ?? 0;

// 6. Hitung sebagai co-promotor
$sql_co_promotor = "SELECT COUNT(DISTINCT r.id_registrasi) as total
                    FROM registrasi r
                    WHERE r.status = 'Diterima' AND r.co_promotor = ?";
$stmt_co_promotor = $conn->prepare($sql_co_promotor);
$stmt_co_promotor->bind_param("i", $id_dosen);
$stmt_co_promotor->execute();
$result_co_promotor = $stmt_co_promotor->get_result();
$co_promotor_data = $result_co_promotor->fetch_assoc();
$stats['sebagai_co_promotor'] = $co_promotor_data['total'] ?? 0;

// 7. Hitung sebagai co-promotor 2  
$sql_co_promotor2 = "SELECT COUNT(DISTINCT r.id_registrasi) as total
                    FROM registrasi r
                    WHERE r.status = 'Diterima' AND r.co_promotor2 = ?";
$stmt_co_promotor2 = $conn->prepare($sql_co_promotor2);
$stmt_co_promotor2->bind_param("i", $id_dosen);
$stmt_co_promotor2->execute();
$result_co_promotor2 = $stmt_co_promotor2->get_result();
$co_promotor2_data = $result_co_promotor2->fetch_assoc();
$stats['sebagai_co_promotor2'] = $co_promotor2_data['total'] ?? 0;

// 8. Hitung sebagai penguji
$sql_penguji = "SELECT COUNT(DISTINCT r.id_registrasi) as total
                FROM registrasi r
                LEFT JOIN jadwal_ujian j ON r.id_registrasi = j.id_registrasi
                WHERE r.status = 'Diterima'
                AND (j.penguji_1 = ? OR j.penguji_2 = ? OR j.penguji_3 = ?)";
$stmt_penguji = $conn->prepare($sql_penguji);
$stmt_penguji->bind_param("iii", $id_dosen, $id_dosen, $id_dosen);
$stmt_penguji->execute();
$result_penguji = $stmt_penguji->get_result();
$penguji_data = $result_penguji->fetch_assoc();
$stats['sebagai_penguji'] = $penguji_data['total'] ?? 0;

// **QUERY RECENT UJIAN YANG SANGAT SEDERHANA**
$sql_recent = "SELECT r.id_registrasi, r.jenis_ujian, r.judul_disertasi,
                      m.nama_lengkap, m.nim, m.program_studi,
                      j.tanggal_ujian, j.tempat,
                      p.id_penilaian, p.nilai_total
               FROM registrasi r
               JOIN mahasiswa m ON r.id_mahasiswa = m.id_mahasiswa
               LEFT JOIN jadwal_ujian j ON r.id_registrasi = j.id_registrasi
               LEFT JOIN penilaian_ujian p ON (r.id_registrasi = p.id_registrasi AND p.id_dosen = ?)
               WHERE r.status = 'Diterima'
               AND (
                   r.promotor = ? OR r.co_promotor = ? OR r.co_promotor2 = ? OR
                   j.promotor = ? OR j.penguji_1 = ? OR j.penguji_2 = ? OR j.penguji_3 = ?
               )
               ORDER BY COALESCE(j.tanggal_ujian, r.tanggal_pengajuan) DESC
               LIMIT 5";

$stmt_recent = $conn->prepare($sql_recent);
if (!$stmt_recent) {
    die("Error preparing recent query: " . $conn->error);
}

$stmt_recent->bind_param("iiiiiiii", 
    $id_dosen,  // 1. penilaian
    $id_dosen,  // 2. promotor registrasi
    $id_dosen,  // 3. co-promotor registrasi
    $id_dosen,  // 4. co-promotor2 registrasi
    $id_dosen,  // 5. promotor jadwal
    $id_dosen,  // 6. penguji 1
    $id_dosen,  // 7. penguji 2
    $id_dosen   // 8. penguji 3
);

$stmt_recent->execute();
$recent_ujian = $stmt_recent->get_result();

$page_title = "Dashboard Dosen - Sistem Disertasi S3 UKSW";
include '../includes/header.php';
include '../includes/sidebar_penguji.php';
?>
<link href="../assets/css/penguji-styles.css" rel="stylesheet">
<div class="main-content">
    <!-- Hero Section - SAMA DENGAN ADMIN -->
    <div class="hero-section" role="banner">
        <div class="hero-content">
            <h1>Dashboard Dosen </h1>
            <p class="hero-subtitle">Sistem Registrasi dan Monitoring Disertasi S3 UKSW</p>
            <p class="hero-breadcrumb" style="margin-top: 12px;">
                <strong><?= htmlspecialchars($dosen['nama_lengkap']) ?></strong>
                <span class="separator">•</span>
                <?= htmlspecialchars($dosen['bidang_keahlian'] ?? 'Dosen') ?>
                <span class="separator">•</span>
                NIDN: <?= htmlspecialchars($dosen['nidn'] ?? '-') ?>
            </p>
            <div style="margin-top: 12px;">
                <?php if ($stats['sebagai_promotor'] > 0): ?>
                    <span class="role-badge-hero">Promotor: <?= $stats['sebagai_promotor'] ?> mhs</span>
                <?php endif; ?>
                <?php if ($stats['sebagai_co_promotor'] > 0): ?>
                    <span class="role-badge-hero">Co-Promotor: <?= $stats['sebagai_co_promotor'] ?> mhs</span>
                <?php endif; ?>
                <?php if ($stats['sebagai_co_promotor2'] > 0): ?>
                    <span class="role-badge-hero">Co-Promotor 2: <?= $stats['sebagai_co_promotor2'] ?> mhs</span>  
                <?php endif; ?>
                <?php if ($stats['sebagai_penguji'] > 0): ?>
                    <span class="role-badge-hero">Penguji: <?= $stats['sebagai_penguji'] ?> ujian</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards (versi rapi seperti admin) -->
        <div class="stats-grid mb-4">
            <div class="card-stat">
                <div class="stat-number"><?= $stats['menunggu_penilaian'] ?></div>
                <div class="stat-label">Menunggu Penilaian</div>
                <a href="daftar_ujian.php?filter=belum_dinilai" class="stat-link">
                    Lihat Detail →
                </a>
            </div>

            <div class="card-stat">
                <div class="stat-number"><?= $stats['sudah_dinilai'] ?></div>
                <div class="stat-label">Sudah Dinilai</div>
                <a href="daftar_ujian.php?filter=sudah_dinilai" class="stat-link">
                    Lihat Detail →
                </a>
            </div>

            <div class="card-stat">
                <div class="stat-number"><?= $stats['revisi_diajukan'] ?></div>
                <div class="stat-label">Revisi Diajukan</div>
                <a href="daftar_ujian.php?filter=revisi_diajukan" class="stat-link">
                    Review →
                </a>
            </div>

            <div class="card-stat">
                <div class="stat-number"><?= $stats['total_ujian'] ?></div>
                <div class="stat-label">Total Ujian</div>
                <a href="daftar_ujian.php" class="stat-link">
                    Lihat Semua →
                </a>
            </div>
        </div>

            <!-- Recent Ujian -->
            <div class="col 12 mb-4">
                <div class="card">
                    <div class="card-header bg-white border-bottom-0 pb-2">
                        <h5 class="mb-0 text-primary">📅 Ujian Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($recent_ujian && $recent_ujian->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($ujian = $recent_ujian->fetch_assoc()): ?>
                                    <div class="list-group-item recent-ujian-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= htmlspecialchars($ujian['nama_lengkap']) ?></h6>
                                                <p class="mb-1 text-muted small">
                                                    <?= htmlspecialchars($ujian['nim']) ?> • 
                                                    <?= ucfirst($ujian['jenis_ujian']) ?>
                                                </p>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="badge badge-peran bg-primary">
                                                        <?= $ujian['jenis_ujian'] ?>
                                                    </span>
                                                    <?php if ($ujian['id_penilaian']): ?>
                                                        <span class="badge badge-peran bg-success">
                                                            ✓ Dinilai: <?= number_format($ujian['nilai_total'] ?? 0, 1) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-peran bg-warning">
                                                            ⏳ Belum Dinilai
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">
                                                    <?= $ujian['tanggal_ujian'] ? date('d/m/Y', strtotime($ujian['tanggal_ujian'])) : 'Belum dijadwalkan' ?>
                                                </small>
                                                <a href="daftar_ujian.php" class="btn btn-sm btn-outline-primary mt-1">
                                                    Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;">📭</div>
                                <p class="text-muted mb-0">Belum ada ujian yang ditugaskan</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info Cards -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">👨‍🎓</div>
                        <h5>Mahasiswa Bimbingan</h5>
                        <p class="mb-2">Total: <strong><?= $stats['sebagai_promotor'] + $stats['sebagai_co_promotor'] + $stats['sebagai_co_promotor2'] ?></strong></p>
                        <small class="text-muted">
                            <?= $stats['sebagai_promotor'] ?> Promotor • <?= $stats['sebagai_co_promotor'] ?> Co-Promotor • <?= $stats['sebagai_co_promotor2'] ?> Co-Promotor 2
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                        <h5>Ujian sebagai Penguji</h5>
                        <p class="mb-2">Total: <strong><?= $stats['sebagai_penguji'] ?></strong></p>
                        <small class="text-muted">
                            Dalam berbagai peran penguji
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎯</div>
                        <h5>Kinerja Penilaian</h5>
                        <p class="mb-2">
                            <?= $stats['total_ujian'] > 0 ? 
                                round(($stats['sudah_dinilai'] / $stats['total_ujian']) * 100) : 0 ?>% Tuntas
                        </p>
                        <small class="text-muted">
                            <?= $stats['sudah_dinilai'] ?> dari <?= $stats['total_ujian'] ?> ujian
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Animasi untuk stat cards
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.card-stat');
    
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<?php include '../includes/footer.php'; ?>