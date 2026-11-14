# DigiBook — Portal Penyewaan E-Book Digital

**UTS Pemrograman Web**  
Semester Ganjil 2026/2027 — Kelas C/D/E  
Program Studi Informatika, Fakultas Teknik  
Universitas Muhammadiyah Malang (UMM)

---

## Anggota Kelompok

| No | Nama Lengkap | NIM |
| :-: | :--- | :--- |
| 1 | Maulana Rayhan Zulkarnaen | 202410370110170 |
| 2 | Danishwara Eka Putra Widiyanto | 202410370110181 |
| 3 | Muhammad Savero Lesmono | 202410370110239 |

---

## Deskripsi Proyek

**DigiBook** adalah prototipe aplikasi web interaktif untuk katalog dan penyewaan buku digital (*e-book*) yang mengintegrasikan antarmuka **Front-End (HTML5, CSS3, Vanilla JavaScript)** dengan **Back-End PHP 8.x berbasis Object-Oriented Programming (OOP)** murni tanpa *framework* maupun database eksternal. Seluruh data koleksi buku dan riwayat transaksi dikelola secara persisten menggunakan format **JSON**.

---

## Fitur Utama Aplikasi

1. **Katalog E-Book Interaktif**
   - Pencarian judul maupun penulis secara *real-time* dan pemfilteran berdasarkan kategori buku.
   - Modal detail buku yang menampilkan sinopsis lengkap, format berkas, jumlah halaman, dan tarif sewa harian.
   - Status ketersediaan buku otomatis (`Tersedia` / `Disewa`) beserta fitur pengembalian satuan maupun reset massal.
2. **Simulasi Penyewaan & Kalkulasi Tarif Otomatis**
   - Validasi input sisi klien menggunakan Vanilla JS (nama lengkap, format email, pilihan buku, dan rentang durasi sewa 1–30 hari) serta validasi ulang di sisi server (PHP).
   - Estimasi rincian biaya otomatis (*Subtotal*, *Potongan Member*, dan *Total Bayar*) secara langsung sebelum transaksi diproses.
3. **Riwayat Transaksi Persisten**
   - Setiap penyewaan menghasilkan kode transaksi unik dan tersimpan otomatis ke `data/transactions.json`.
4. **Antarmuka Klasik & Responsif**
   - Menggunakan struktur elemen semantik HTML5 dengan tema *Classic Library* (krem kertas & cokelat hangat) yang responsif di perangkat desktop maupun mobile.

---

## Penerapan Konsep PHP OOP

- **Namespace & PSR-4 Autoloading (`autoload.php`):** Mengorganisasi kelas ke dalam namespace `App\Interfaces`, `App\Models`, dan `App\Services`.
- **Interface & Abstraction:** Kontrak perilaku objek untuk entitas media dan kalkulasi penyewaan.
- **Encapsulation:** Penggunaan *access modifier* `private` dan `protected` pada properti kelas dengan metode *getter* dan *setter* tervalidasi.
- **Inheritance & Polymorphism:** Kelas induk `User` diturunkan menjadi `RegularMember` (tarif normal / diskon 0%) dan `PremiumMember` (potongan harga 20%), di mana metode perhitungan biaya sewa di-*override* secara polimorfik sesuai tipe keanggotaan peminjam.

---

## Struktur Direktori Proyek

```text
digibook/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── picture/
├── data/
│   ├── ebooks.json
│   └── transactions.json
├── src/
│   ├── Interfaces/
│   ├── Models/
│   └── Services/
├── autoload.php
├── index.php
└── README.md
```

---

## Cara Menjalankan Aplikasi

Pastikan **PHP versi 8.0 atau lebih baru** sudah terpasang di komputer Anda.

### Opsi 1: Menggunakan PHP Built-in Server (Direkomendasikan)
1. Buka terminal / Command Prompt / PowerShell, lalu arahkan ke folder utama proyek:
   ```bash
   cd path/to/folder-proyek
   ```
2. Jalankan server lokal PHP:
   ```bash
   php -S localhost:8000
   ```
3. Buka browser dan akses alamat berikut:
   ```text
   http://localhost:8000
   ```

### Opsi 2: Menggunakan Laragon / XAMPP
1. Salin seluruh folder proyek ke dalam direktori web server:
   - **Laragon:** `C:\laragon\www\digibook`
   - **XAMPP:** `C:\xampp\htdocs\digibook`
2. Nyalakan modul **Apache** pada Laragon atau XAMPP Control Panel.
3. Buka browser dan akses:
   ```text
   http://localhost/digibook
   ```

> **Catatan:** Pastikan folder `data/` beserta berkas `ebooks.json` dan `transactions.json` memiliki izin baca/tulis (*read/write permission*) agar perubahan status buku dan riwayat transaksi dapat tersimpan.