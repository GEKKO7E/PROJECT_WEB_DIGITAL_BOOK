<?php
declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

use App\Models\RegularMember;
use App\Models\PremiumMember;
use App\Services\RentalService;

$service = new RentalService(
    __DIR__ . '/data/ebooks.json',
    __DIR__ . '/data/transactions.json'
);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'rent') {
            $name = trim($_POST['borrower_name'] ?? '');
            $email = trim($_POST['borrower_email'] ?? '');
            $memberTier = $_POST['member_tier'] ?? 'regular';
            $ebookId = $_POST['ebook_id'] ?? '';
            $days = (int) ($_POST['days'] ?? 0);

            if ($name === '' || $email === '' || $ebookId === '') {
                throw new Exception('Semua kolom wajib diisi.');
            }

            $user = ($memberTier === 'premium')
                ? new PremiumMember($name, $email)
                : new RegularMember($name, $email);

            $trx = $service->rentEbook($user, $ebookId, $days);
            $trxData = $trx->toArray();

            $message = sprintf(
                'Penyewaan berhasil! Kode Transaksi: <strong>%s</strong> &bull; Total Bayar: <strong>Rp %s</strong> (%s)',
                htmlspecialchars($trxData['transactionId']),
                number_format($trxData['totalCost'], 0, ',', '.'),
                htmlspecialchars($trxData['memberType'])
            );
            $messageType = 'success';
        } elseif ($action === 'return_one') {
            $ebookId = $_POST['ebook_id'] ?? '';
            $service->returnSingleEbook($ebookId);
            $message = 'E-Book (' . htmlspecialchars($ebookId) . ') berhasil dikembalikan ke rak katalog.';
            $messageType = 'info';
        } elseif ($action === 'reset') {
            $service->returnAllEbooks();
            $message = 'Seluruh koleksi e-book telah dikembalikan menjadi Tersedia.';
            $messageType = 'info';
        }
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage());
        $messageType = 'error';
    }
}

$ebooks = $service->getAllEbooks();
$transactions = $service->getAllTransactions();

$totalBooks = count($ebooks);
$availableBooks = count(array_filter($ebooks, fn($b) => $b->isAvailable()));
$rentedBooks = $totalBooks - $availableBooks;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiBook &mdash; Portal Penyewaan E-Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-inner">
            <a href="#" class="brand">
                <span class="brand-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                </span>
                <div>
                    <span class="brand-title">Digi<strong>Book</strong></span>
                    <span class="brand-sub">Sewa Buku Digital</span>
                </div>
            </a>
            <nav aria-label="Navigasi Utama" class="nav-links">
                <a href="#katalog">Katalog</a>
                <a href="#sewa">Sewa</a>
                <a href="#riwayat">Riwayat <span class="nav-pill"><?= count($transactions) ?></span></a>
            </nav>
        </div>
    </header>

    <section class="hero-banner">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow">DIGITAL BOOK</span>
                <h1>Perpustakaan Digital &amp; Penyewaan E-Book</h1>
                <p>Eksplorasi koleksi buku referensi rekayasa perangkat lunak, desain sistem, dan ilmu komputer dengan perhitungan tarif otomatis berbasis <em>Polymorphism</em> keanggotaan.</p>
                <div class="hero-actions">
                    <a href="#katalog" class="btn btn-light">Jelajahi Katalog</a>
                    <a href="#sewa" class="btn btn-glass">Mulai Sewa E-Book</a>
                </div>
            </div>
            <div class="stats-panel" aria-label="Ringkasan Katalog">
                <div class="stat-card">
                    <span class="stat-label">Total Koleksi</span>
                    <strong class="stat-value"><?= $totalBooks ?></strong>
                    <span class="stat-hint">Judul E-Book</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Tersedia</span>
                    <strong class="stat-value text-emerald"><?= $availableBooks ?></strong>
                    <span class="stat-hint">Siap Disewa</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Sedang Disewa</span>
                    <strong class="stat-value text-amber"><?= $rentedBooks ?></strong>
                    <span class="stat-hint">Aktif Dipinjam</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Transaksi</span>
                    <strong class="stat-value text-sky"><?= count($transactions) ?></strong>
                    <span class="stat-hint">Tercatat di JSON</span>
                </div>
            </div>
        </div>
    </section>

    <main class="container main-content">
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>" role="alert">
                <span class="alert-dot"></span>
                <div><?= $message ?></div>
            </div>
        <?php endif; ?>

        <section id="katalog" class="section-block">
            <div class="section-top">
                <div>
                    <h2>Katalog Koleksi E-Book</h2>
                    <p class="section-desc">Klik <strong>Detail</strong> untuk melihat sinopsis atau <strong>Pilih Buku</strong> untuk langsung menyewa.</p>
                </div>
                <div class="filter-toolbar">
                    <div class="search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="search" id="searchInput" placeholder="Cari judul atau penulis..." aria-label="Cari e-book">
                    </div>
                    <select id="categoryFilter" aria-label="Filter kategori">
                        <option value="all">Semua Kategori</option>
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Web Programming">Web Programming</option>
                        <option value="System Design">System Design</option>
                        <option value="Computer Science">Computer Science</option>
                    </select>
                </div>
            </div>

            <div class="ebook-grid" id="ebookGrid">
                <?php foreach ($ebooks as $ebook): ?>
                    <article class="ebook-card"
                             data-title="<?= htmlspecialchars(strtolower($ebook->getTitle())) ?>"
                             data-author="<?= htmlspecialchars(strtolower($ebook->getAuthor())) ?>"
                             data-category="<?= htmlspecialchars($ebook->getCategory()) ?>">
                        <div class="book-cover has-image" style="background: <?= htmlspecialchars($ebook->getCoverGradient()) ?>;">
                            <?php if ($ebook->getCoverImage() !== ''): ?>
                                <img src="<?= htmlspecialchars($ebook->getCoverImage()) ?>"
                                     alt="Sampul <?= htmlspecialchars($ebook->getTitle()) ?>"
                                     class="cover-img"
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                            <div class="cover-overlay"></div>

                            <div class="cover-top">
                                <span class="cover-id"><?= htmlspecialchars($ebook->getId()) ?></span>
                                <span class="status-pill <?= $ebook->isAvailable() ? 'pill-ready' : 'pill-rented' ?>">
                                    <?= $ebook->isAvailable() ? 'Tersedia' : 'Disewa' ?>
                                </span>
                            </div>
                            <div class="cover-title-preview">
                                <span class="cover-cat"><?= htmlspecialchars($ebook->getCategory()) ?></span>
                            </div>
                        </div>

                        <div class="card-body">
                            <h3><?= htmlspecialchars($ebook->getTitle()) ?></h3>
                            <p class="author">oleh <strong><?= htmlspecialchars($ebook->getAuthor()) ?></strong></p>

                            <div class="spec-tags">
                                <span><?= htmlspecialchars($ebook->getFormat()) ?></span>
                                <span>&bull;</span>
                                <span><?= $ebook->getPages() ?> Halaman</span>
                            </div>

                            <div class="card-bottom">
                                <div class="price-tag">
                                    <span class="price-amount">Rp <?= number_format($ebook->getDailyRate(), 0, ',', '.') ?></span>
                                    <span class="price-unit">/ hari</span>
                                </div>

                                <div class="card-actions">
                                    <button type="button" class="btn btn-ghost btn-sm detail-btn"
                                            data-id="<?= htmlspecialchars($ebook->getId()) ?>"
                                            data-title="<?= htmlspecialchars($ebook->getTitle()) ?>"
                                            data-author="<?= htmlspecialchars($ebook->getAuthor()) ?>"
                                            data-category="<?= htmlspecialchars($ebook->getCategory()) ?>"
                                            data-pages="<?= $ebook->getPages() ?>"
                                            data-format="<?= htmlspecialchars($ebook->getFormat()) ?>"
                                            data-rate="Rp <?= number_format($ebook->getDailyRate(), 0, ',', '.') ?> / hari"
                                            data-synopsis="<?= htmlspecialchars($ebook->getSynopsis()) ?>">
                                        Detail
                                    </button>

                                    <?php if ($ebook->isAvailable()): ?>
                                        <button type="button" class="btn btn-primary btn-sm select-ebook-btn"
                                                data-id="<?= htmlspecialchars($ebook->getId()) ?>">
                                            Pilih Buku
                                        </button>
                                    <?php else: ?>
                                        <form method="POST" action="index.php" style="display:inline;">
                                            <input type="hidden" name="action" value="return_one">
                                            <input type="hidden" name="ebook_id" value="<?= htmlspecialchars($ebook->getId()) ?>">
                                            <button type="submit" class="btn btn-amber btn-sm" title="Kembalikan buku ini">Kembalikan</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p id="emptySearchMsg" class="empty-search" style="display:none;">Tidak ada e-book yang sesuai dengan kata kunci pencarian Anda.</p>
        </section>

        <div class="workspace-grid">
            <section id="sewa" class="section-block form-card">
                <div class="card-header-accent">
                    <h2>Form Penyewaan E-Book</h2>
                </div>

                <form method="POST" action="index.php" id="rentalForm" novalidate>
                    <input type="hidden" name="action" value="rent">

                    <div class="form-group">
                        <label for="borrower_name">Nama Lengkap Peminjam</label>
                        <input type="text" id="borrower_name" name="borrower_name" placeholder="Masukkan nama lengkap..." required>
                        <small class="error-text" id="nameError"></small>
                    </div>

                    <div class="form-group">
                        <label for="borrower_email">Alamat Email</label>
                        <input type="email" id="borrower_email" name="borrower_email" placeholder="nama@webmail.umm.ac.id" required>
                        <small class="error-text" id="emailError"></small>
                    </div>

                    <div class="form-group">
                        <label for="member_tier">Tipe Keanggotaan <em>(Polymorphism)</em></label>
                        <select id="member_tier" name="member_tier">
                            <option value="regular" data-discount="0">Regular Member &mdash; Tarif Normal (0% Diskon)</option>
                            <option value="premium" data-discount="0.2">Premium Member &mdash; Hemat 20% Setiap Sewa</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ebook_id">E-Book yang Disewa</label>
                        <select id="ebook_id" name="ebook_id" required>
                            <option value="" data-rate="0">-- Pilih E-Book Tersedia --</option>
                            <?php foreach ($ebooks as $ebook): ?>
                                <?php if ($ebook->isAvailable()): ?>
                                    <option value="<?= htmlspecialchars($ebook->getId()) ?>" data-rate="<?= $ebook->getDailyRate() ?>">
                                        [<?= htmlspecialchars($ebook->getId()) ?>] <?= htmlspecialchars($ebook->getTitle()) ?> &mdash; Rp <?= number_format($ebook->getDailyRate(), 0, ',', '.') ?>/hari
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="error-text" id="ebookError"></small>
                    </div>

                    <div class="form-group">
                        <label for="days">Durasi Peminjaman (1 &ndash; 30 Hari)</label>
                        <input type="number" id="days" name="days" min="1" max="30" value="3" required>
                        <small class="error-text" id="daysError"></small>
                    </div>

                    <div class="summary-receipt">
                        <div class="receipt-row">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">Rp 0</span>
                        </div>
                        <div class="receipt-row text-emerald">
                            <span>Potongan Member</span>
                            <span id="discountDisplay">- Rp 0</span>
                        </div>
                        <div class="receipt-divider"></div>
                        <div class="receipt-total">
                            <span>Total Estimasi Bayar</span>
                            <strong id="estimatedTotal">Rp 0</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Konfirmasi &amp; Proses Sewa</button>
                </form>
            </section>

            <section id="riwayat" class="section-block history-card">
                <div class="section-top">
                    <div>
                        <h2>Riwayat Transaksi</h2>
                    </div>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="reset">
                        <button type="submit" class="btn btn-outline btn-sm">Reset Semua Buku</button>
                    </form>
                </div>

                <?php if (empty($transactions)): ?>
                    <div class="empty-box">
                        <p>Belum ada riwayat transaksi penyewaan.</p>
                        <span>Silakan isi formulir di samping untuk mensimulasikan transaksi pertama.</span>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kode &amp; Waktu</th>
                                    <th>Peminjam</th>
                                    <th>E-Book</th>
                                    <th>Durasi</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $trx): ?>
                                    <tr>
                                        <td>
                                            <span class="trx-badge"><?= htmlspecialchars($trx['transactionId']) ?></span>
                                            <small class="block-muted"><?= htmlspecialchars($trx['createdAt'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($trx['borrowerName']) ?></strong>
                                            <small class="block-muted"><?= htmlspecialchars($trx['memberType']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($trx['mediaTitle']) ?></td>
                                        <td><?= (int) $trx['days'] ?> hari</td>
                                        <td><strong class="price-cell">Rp <?= number_format((float) $trx['totalCost'], 0, ',', '.') ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <div class="modal-backdrop" id="detailModal" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="modal-header">
                <span class="badge-cat" id="modalCategory">Kategori</span>
                <button type="button" class="modal-close" id="closeModalBtn" aria-label="Tutup modal">&times;</button>
            </div>
            <h3 id="modalTitle">Judul E-Book</h3>
            <p class="modal-author" id="modalAuthor">Penulis</p>
            <p class="modal-synopsis" id="modalSynopsis">Sinopsis buku.</p>
            <div class="modal-specs">
                <div><span>Format File</span><strong id="modalFormat">PDF</strong></div>
                <div><span>Jumlah Halaman</span><strong id="modalPages">0</strong></div>
                <div><span>Tarif Sewa</span><strong id="modalRate">Rp 0</strong></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="closeModalFooterBtn">Tutup</button>
                <button type="button" class="btn btn-primary" id="modalSelectBtn">Pilih &amp; Sewa E-Book Ini</button>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <div class="container footer-inner">
            <p>&copy; 2026 <strong>DigiBook</strong> &mdash; Digital Book</p>
            <p class="footer-tech">Digital Book</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>