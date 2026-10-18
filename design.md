# 📐 DOKUMEN SPESIFIKASI DESAIN: REDESIGN LAYOUT MOBILE HALAMAN ATLET
**Proyek:** Database MSC (Marabunta Swimming Club)  
**Dokumen:** `design.md`  
**Tujuan:** Panduan teknis dan visual implementasi layout mobile halaman Data Atlet (`athletes.index` dan `athletes.show`) berdasarkan acuan referensi desain, dengan mempertahankan 100% *colour palette* asli aplikasi (*Dark Reflective Premium*). Pola ini dirancang sebagai **Template Standar Reusable** untuk modul lainnya.

---

## 1. Prinsip & Filosofi Desain

1. **Mempertahankan Identitas Warna (*Zero Color Change*)**:
   - Seluruh warna tetap mengacu pada token tema gelap premium MSC:
     - Background Halaman: `--bg-page` (`#060f22` / Deep Navy)
     - Card Background: `--card-bg` (`rgba(10, 22, 40, 0.85)` dengan border `rgba(91, 168, 232, 0.1)`)
     - Warna Utama (Primary): `--primary` (`#2684d4` / Oceanic Electric Blue)
     - Aksen / Sorotan: `--accent` (`#c9a84c` / Gold Sand)
     - Badge Aktif / Sukses: Emerald Green (`rgba(16, 185, 129, 0.15)` dengan teks `#10b981`)
     - Badge Non-Aktif / Netral: Muted Gray (`rgba(142, 184, 216, 0.15)` dengan teks `#8eb8d8`)
2. **Mobile-First Card & Sheet Pattern**:
   - Pada layar **$\le$ 768px**, tabel horizontal desktop disembunyikan (`display: none`), digantikan dengan **Vertical Athlete Cards** yang ramah jempol (*thumb-friendly*).
   - Pada layar **> 768px (Desktop & Tablet)**, tampilan tabel data asli tetap utuh dan fungsional tanpa perubahan.
3. **Pola Modular / Reusable Template**:
   - Komponen seperti **Search-Filter Bar**, **Filter Chips**, **Bottom Sheet Modal**, dan **Accordion Info Card** dirancang dengan CSS class mandiri sehingga dapat digunakan langsung pada modul Kompetisi, Absensi, maupun Laporan.

---

## 2. Struktur Layout Mobile: Halaman Daftar Atlet (`athletes.index`)

### A. Header, Pencarian, & Filter Bar (Satu Baris Fleksibel)
Mengikuti gambar referensi (Panel Kiri Atas):

```text
+-----------------------------------------------+
| Atlit                                    [➕] |  <- Header ringkas
+-----------------------------------------------+
| [ 🔍 Cari Atlet...           ] [ 🌪️ Filter ] |  <- 1 Baris fleksibel
+-----------------------------------------------+
| [x] Prestasi   [x] KU 2012   [Reset Semua]    |  <- Filter Chips
+-----------------------------------------------+
```

* **Header Ringkas**:
  - Judul: **Atlit** (font weight 800, ukuran `1.4rem`, warna `--text-dark`).
  - Tombol aksi "+ Tambah" hadir sebagai tombol ikon atau tombol kompak di sudut kanan atas.
* **Search & Filter Bar (1 Baris)**:
  - Input pencarian menggunakan ikon kaca pembesar di sisi kiri, sudut membulat (`border-radius: 0.75rem`), background `--navy-800`, dan memenuhi sisa lebar layar (`flex: 1`).
  - Tombol **Filter** berada di samping kanan pencarian dengan ikon corong (`flex-shrink: 0`), ukuran padding proporsional (`0.75rem 1rem`).
* **Active Filter Chips (Di Bawah Input Pencarian)**:
  - Ditampilkan hanya jika ada filter aktif (misal `class_id`, `ku`, atau `search`).
  - Bentuk: Kapsul kecil (*pill*) dengan tombol `✕` di dalamnya untuk menghapus filter tersebut secara mandiri.
  - Terdapat tombol teks kecil `Reset` untuk membersihkan semua filter sekaligus.

---

### B. Kartu Atlet Vertikal (Athlete Card Component)
Menggantikan baris tabel menjadi kartu interaktif dengan susunan hierarki yang jelas:

```text
+-------------------------------------------------------------+
|  [ Foto ]   ADI KURNIAWAN                  [ Aktif ]        |
|   Bulat     ID: AT-001                                      |
|             Kelas Prestasi • L (Putera)                     |
|                                                             |
|  ---------------------------------------------------------  |
|  Detail & Aksi                                         [ v ]| <- Accordion Chevron
+-------------------------------------------------------------+
```

* **Area Utama (Selalu Terlihat)**:
  - **Sisi Kiri**: Foto atlet (ukuran `52px x 52px`, `border-radius: 50%`, `object-fit: cover`, border `1px solid var(--border-color)`). Jika belum ada foto, tampilkan inisial nama atau avatar siluet default.
  - **Sisi Tengah**: 
    - Nama Atlet: Huruf tebal kapital (`font-size: 1rem`, `font-weight: 700`, warna `--text-dark`).
    - ID Atlet: Nomor unik atlet (misal: `#MSC-001`).
    - Sub-keterangan: Nama Kelas Renang (misal: "Prestasi") dan Gender / KU (misal: "PA (Putera) • KU 2012").
  - **Sisi Kanan Atas**: Badge status:
    - `Aktif`: Latar hijau lembut dengan teks hijau emerald.
    - `Non-Aktif`: Latar abu-abu gelap dengan teks muted.
* **Area Expandable (Accordion Bawah Kartu)**:
  - Baris pemicu bertuliskan **"Detail & Aksi"** disertai ikon chevron down (`⌄`).
  - Saat ditekan, kartu membuka panel tersembunyi dengan animasi *smooth slide-down*:
    - Tanggal Lahir & Usia.
    - Nama Orang Tua / Wali beserta nomor kontak.
    - 3 Tombol Aksi Cepat:
      1. **Lihat Profil** (`btn-primary` kecil $\rightarrow$ menuju `athletes.show`).
      2. **Riwayat Event** (`btn-info` kecil $\rightarrow$ menuju `event-history.show`).
      3. **Edit** (`btn-warning` kecil $\rightarrow$ menuju `athletes.edit`).

---

### C. Filter Bottom Sheet Modal
Mengikuti referensi gambar (Panel Kanan Atas & Kiri Bawah):

* **Interaksi Muncul**:
  - Saat tombol **"Filter"** ditekan, panel meluncur halus (*slide-up*) dari bagian bawah layar.
  - Background utama dilapisi overlay gelap transparan (`rgba(4, 13, 26, 0.75)` dengan efek `backdrop-filter: blur(4px)`).
* **Desain Panel**:
  - `border-radius: 1.5rem 1.5rem 0 0` (sudut atas membulat).
  - Background: `--navy-900` dengan border halus di bagian atas.
  - **Drag Handle**: Garis abu-abu kecil di bagian tengah atas (`width: 40px`, `height: 4px`, `border-radius: 2px`).
  - Header Sheet: Judul **"Filter Data Atlet"** dan tombol tutup `✕`.
* **Elemen Form Filter (Full Width)**:
  1. **Kelas Latihan**: Dropdown select untuk memilih kelas renang (`Semua Kelas`, `Pemula`, `Prestasi`, dll.).
  2. **Kelompok Umur (KU)**: Dropdown select daftar tahun kelahiran KU.
  3. **Jenis Kelamin**: Dropdown select (`Semua`, `Putera (L)`, `Puteri (P)`).
* **Footer Bottom Sheet**:
  - Tombol **"Terapkan Filter"** (lebar 100%, background `--primary`, teks tebal, ikon corong).
  - Tombol **"Reset Filter"** di samping atau bawahnya.

---

## 3. Struktur Layout Mobile: Halaman Detail Atlet (`athletes.show`)
Mengikuti referensi gambar (Panel Kanan Bawah):

### A. Top Identity & Edit Action
- **Judul Utama**: Nama atlet berukuran besar dan tegas (`font-size: 1.5rem`, `font-weight: 800`).
- **Tombol Edit**: Tombol lebar penuh (*full-width*) tepat di bawah nama atlet bertuliskan **"Edit Data Atlet"** dengan warna `--primary` atau aksen.
- **Badge Info Cepat**: Di bawah tombol, terdapat deretan badge kecil: KU, Gender (PA/PI), dan Kelas saat ini.

### B. Format Kartu Accordion Satu Kolom
Menggantikan tabel-tabel lebar menjadi kartu accordion lipat yang bersih:

1. **Accordion 1: Informasi Pribadi (Default: Terbuka / Expanded)**
   - Header bertuliskan "Informasi Pribadi" dengan ikon panah atas (`⌃`).
   - Konten: Grid 2 kolom yang rapi:
     - Tanggal Lahir
     - Usia / KU
     - Tanggal Bergabung
     - Orang Tua / Wali
     - No. Kontak WhatsApp Orang Tua
2. **Accordion 2: Catatan Waktu Terbaik / Personal Best (Default: Terbuka)**
   - Header bertuliskan "Prestasi & Waktu Terbaik (PB)" disertai ikon piala 🏆.
   - Konten: Kartu mini nomor lomba (Gaya Dada 50m, Gaya Bebas 50m, dll.) yang mencantumkan:
     - Catatan Waktu (misal: `00.29.85`).
     - Nama Kejuaraan & Tanggal capaian.
     - Badge peringkat / medali 🥇🥈🥉 jika ada.
3. **Accordion 3: Riwayat Kenaikan Kelas (Default: Tertutup / Collapsed)**
   - Header bertuliskan "Riwayat Kelas & Latihan".
   - Konten: *Timeline* vertikal mutasi kelas beserta catatan pelatih.

### C. Sticky Bottom Action: Hubungi Orang Tua / Atlet
- Tombol aksi tetap (*sticky bottom bar*) bertuliskan **"Hubungi Orang Tua / Atlet"** dengan ikon WhatsApp.
- Jika diklik, langsung membuka tautan `https://wa.me/{nomor}` orang tua yang bersangkutan.
- Diberi margin aman (*safe-area-inset-bottom*) agar tidak tertutup bar navigasi browser smartphone.

---

## 4. Spesifikasi Token & Variabel CSS

Desain memanfaatkan variabel warna yang sudah aktif di `public/css/custom-style.css`:

```css
/* Token Warna Tetap (Eksisting) */
--primary: #2684d4;
--primary-hover: #1a6bb5;
--bg-page: #060f22;
--navy-900: #060f22;
--navy-800: #0a1628;
--card-bg: rgba(10, 22, 40, 0.85);
--text-dark: #edf4fc;
--text-muted: #8eb8d8;
--border-color: rgba(91, 168, 232, 0.1);

/* Variabel Tambahan Khusus Mobile Component */
--mobile-sheet-bg: #0a1628;
--mobile-sheet-overlay: rgba(4, 13, 26, 0.78);
--mobile-chip-bg: rgba(38, 132, 212, 0.15);
--mobile-chip-border: rgba(38, 132, 212, 0.3);
--mobile-card-radius: 1rem;
--mobile-sheet-radius: 1.5rem;
```

---

## 5. Matriks Breakpoint & Responsivitas

| Breakpoint | Target Perangkat | Layout Daftar Atlet | Layout Filter | Layout Detail Atlet |
| :--- | :--- | :--- | :--- | :--- |
| **< 768px** | Smartphone (320px – 480px, 768px) | **Vertical Card List** + Accordion Aksi Cepat | **Bottom Sheet Modal** dengan Drag Handle | **One-Column Accordion** + Sticky WhatsApp Button |
| **$\ge$ 768px** | Tablet & Desktop (> 768px) | **Data Table Lengkap** (Tampilan asli tetap utuh) | **Inline Filter Bar** (Tampilan asli tetap utuh) | **Multi-Column Profile Grid & Full Data Cards** (Asli) |

---

## 6. Arsitektur Komponen & File yang Akan Dikerjakan (Setelah Perintah)

Ketika implementasi disetujui, perubahan kode hanya menyentuh:

1. **`public/css/custom-style.css`**:
   - Menambahkan utility classes untuk mobile card, bottom sheet, chips, dan accordion di dalam blok `@media (max-width: 768px)`.
   - Mengatur switch tampilan (`.desktop-table-view` vs `.mobile-card-view`).
2. **`resources/views/admin/athletes/index.blade.php`**:
   - Membungkus tabel eksisting dalam container desktop `.desktop-table-view`.
   - Menambahkan struktur `.mobile-card-view` yang me-loop `$athletes`.
   - Menambahkan markup `#filterBottomSheet` dan script interaksi ringan (toggle filter modal & expand accordion).
3. **`resources/views/admin/athletes/show.blade.php`**:
   - Menambahkan layout accordion mobile dengan CSS toggle murni / JS ringan tanpa mengganggu tampilan desktop.

---

## 7. Rencana Verifikasi & Pengujian
Setelah implementasi dilakukan nantinya:
1. Menjalankan script automated test suite `.\run-tests.ps1` untuk memastikan tidak ada rute, controller, atau logika bisnis yang terganggu.
2. Melakukan pengujian visual menggunakan browser subagent pada resolusi layar:
   - **375 x 667 px** (iPhone SE)
   - **390 x 844 px** (iPhone 13 / 14)
   - **430 x 932 px** (iPhone Pro Max)
   - **1280 x 800 px** (Desktop - memastikan layout desktop tidak berubah).

---
*Dokumen ini berstatus **DRAFT SIAP EKSEKUSI**. Tidak ada berkas aplikasi lain yang diubah sampai Anda memberikan perintah untuk memulai implementasi.*
