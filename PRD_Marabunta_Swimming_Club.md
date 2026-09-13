# Product Requirements Document (PRD)
# Sistem Database Atlet — Marabunta Swimming Club (MSC)

**Versi Dokumen:** 1.0
**Tanggal:** Juli 2026
**Status:** Draft untuk Review

---

## 1. Ringkasan Eksekutif

Marabunta Swimming Club (MSC) membutuhkan sebuah sistem informasi berbasis web untuk mengelola data atlet renang, mencatat hasil perlombaan, dan melacak *Personal Best Time* (PB) setiap atlet. Sistem ini akan digunakan oleh dua jenis pengguna utama: **Pelatih (Admin)** yang mengelola seluruh data, dan **Orang Tua (User)** yang memantau perkembangan anaknya.

**Tech Stack:**
- **Backend:** Laravel 11.x (PHP 8.2+)
- **Frontend:** Blade Templating + HTML5 + CSS3 (custom, clean modern design)
- **Database:** MySQL / MariaDB
- **Autentikasi:** Laravel Breeze/Fortify (role-based: admin & user)
- **Chart/Visualisasi:** Chart.js (untuk grafik progres PB time)

---

## 2. Tujuan Produk

1. Memusatkan data atlet dari seluruh kelas (Dasar 0, Dasar 1, Pemula, Pra-Prestasi, Prestasi) dalam satu sistem.
2. Memudahkan pelatih mencatat hasil lomba dan memantau perkembangan atlet secara objektif berbasis data.
3. Memberikan transparansi kepada orang tua mengenai progres latihan dan prestasi anaknya.
4. Menjadi dasar data untuk keputusan kenaikan kelas atlet.

---

## 3. Target Pengguna & Role

| Role | Deskripsi | Hak Akses |
|---|---|---|
| **Super Admin (Pelatih Kepala)** | Mengelola seluruh sistem | Full access: kelola atlet, kelas, lomba, hasil, akun user, laporan |
| **Admin (Pelatih)** | Pelatih kelas tertentu | Kelola atlet & hasil lomba di kelas yang diampu |
| **User (Orang Tua)** | Wali dari atlet | Hanya bisa melihat data anak sendiri (read-only) |

---

## 4. Struktur Kelas Atlet

Sistem harus mendukung 5 tingkatan kelas dengan kemungkinan atlet naik kelas seiring waktu:

1. **Dasar 0** — Tahap pengenalan air
2. **Dasar 1** — Tahap dasar teknik renang
3. **Pemula** — Penguasaan 4 gaya dasar
4. **Pra-Prestasi** — Persiapan kompetisi
5. **Prestasi** — Atlet kompetitif aktif

Setiap perpindahan kelas dicatat sebagai riwayat (histori), bukan menimpa data lama.

### 4.1 KU (Kelompok Umur)

Selain kelas latihan, setiap atlet juga memiliki **KU (Kelompok Umur)** yang dipakai khusus untuk keperluan perlombaan resmi (bukan untuk kelas latihan sehari-hari). KU **dihitung otomatis oleh sistem** dari tahun lahir atlet, tidak perlu diinput manual.

**Aturan:**
- KU = tahun lahir atlet. Contoh: atlet lahir tahun 2015 → **KU 2015**.
- Nilai ini otomatis muncul begitu tanggal lahir diinput di form atlet, dan otomatis tampil di semua form/laporan (termasuk saat export ke Excel format perlombaan).
- KU dipakai sebagai salah satu filter saat admin memilih atlet untuk didaftarkan ke suatu event, karena kategori lomba biasanya dikelompokkan per KU (sama seperti kolom "KU" pada Form A1-A3 di contoh file Excel klub).

---

## 5. Daftar Fitur Lengkap

### 5.1 Modul Autentikasi
- Halaman **Login** (admin & user, satu pintu masuk dengan redirect sesuai role)
- Halaman **Register** khusus orang tua (dengan verifikasi admin sebelum aktif, agar tidak sembarang orang bisa daftar)
- **Lupa Password** (reset via email)
- **Multi-anak dalam satu akun** — satu orang tua bisa memiliki lebih dari satu anak terdaftar

### 5.2 Modul Manajemen Atlet (Admin)
- **Input Atlet Baru**: nama lengkap, tanggal lahir, jenis kelamin, foto, kelas saat ini, tanggal bergabung, nama & kontak orang tua
- **Edit/Hapus Data Atlet**
- **Riwayat Kenaikan Kelas**: catatan kapan atlet naik dari satu kelas ke kelas berikutnya + catatan pelatih
- **Pencarian & Filter Atlet** (berdasarkan nama, kelas, usia, jenis kelamin)
- **Import/Export data atlet** (Excel/CSV) untuk kebutuhan migrasi data awal

### 5.3 Modul Event & Entry Perlombaan

Modul ini mengikuti alur kerja nyata klub: **buat event → daftarkan atlet & nomor lomba → input catatan waktu saat event berlangsung → export ke Excel format resmi**.

**a. Buat Event Baru (Admin)**
- Input: nama event (contoh: *"Danlanud Cup"*), penyelenggara, lokasi, tanggal mulai & selesai (contoh: 18–19 Juni 2026), status (Akan Datang / Berlangsung / Selesai)
- Satu event bisa berlangsung lebih dari satu hari

**b. Pendaftaran Atlet ke Event (Entry)**
- Admin memilih atlet dari daftar (bisa difilter per kelas/KU) untuk didaftarkan ke event tertentu
- Untuk setiap atlet yang didaftarkan, admin memilih **nomor lomba** yang diikuti (bisa lebih dari satu nomor per atlet), mengikuti struktur yang sama dengan Form A1-A3:
  - Gaya Bebas (50m, 100m, 200m)
  - Gaya Kupu-Kupu (50m, 100m)
  - Gaya Punggung (50m, 100m)
  - Gaya Dada (50m, 100m)
  - Gaya Ganti (200m)
  - Estafet 4×50 (Gaya Bebas Mix, Gaya Ganti Mix)
- Sistem menyimpan data ini sebagai **daftar entry** — semacam "tiket ikut lomba" per atlet per nomor, terpisah dari data hasil lomba

**c. Input Catatan Waktu Saat Event Berlangsung**
- Saat event berjalan, admin/pelatih membuka halaman event tersebut dan menginput catatan waktu langsung ke slot nomor lomba yang sudah didaftarkan per atlet
- Format input waktu mengikuti standar klub: **6 digit, contoh 00.32.94 / 02.34.88** (menit.detik.desimal), sesuai catatan pada Form A1-A3
- Atlet yang tidak start atau didiskualifikasi bisa ditandai (DNS/DQ) tanpa mengisi waktu
- Setelah waktu diinput, sistem otomatis mengecek apakah ini **PB baru** untuk atlet tersebut

**d. Validasi Otomatis PB**
- Sistem mendeteksi apabila hasil baru lebih cepat dari PB sebelumnya di nomor yang sama → otomatis tersimpan sebagai PB baru + notifikasi ke orang tua atlet

### 5.3.1 Export Hasil Event ke Excel (Format A1-A3)

- Admin dapat mengekspor hasil suatu event menjadi file Excel dengan struktur identik seperti Form A1/A2/A3 yang sudah dipakai klub selama ini, sehingga tidak perlu ubah format pelaporan yang sudah familiar bagi pelatih.
- **Nama file otomatis** mengikuti pola: `[Kode Form] [Nama Event]`, contoh: **"A3 Event Danlanud"** untuk kelas Pemula pada event Danlanud Cup.
- **Struktur kolom export** (mengikuti contoh gambar):
  - Kolom NO, NAMA, LAHIR, KU, PA/PI
  - Kolom per nomor lomba dikelompokkan per gaya (Gaya Bebas, Gaya Kupu-Kupu, Gaya Punggung, Gaya Dada, Gaya Ganti) dan per jarak (50M, 100M, 200M) sebagai sub-header
  - Kolom Estafet 4×50 untuk tiap kategori mix
  - Waktu ditulis dalam format 6 digit sesuai standar klub
- Jika satu event memiliki atlet dari beberapa kelas (misal Pemula & Pra-Prestasi), sistem bisa generate **beberapa sheet/file sekaligus** (A1, A2, A3, dst) sesuai kelas, seperti tab "FORM-A1", "FORM-A3" pada contoh file.
- Export tersedia dalam format **.xlsx**, siap dicetak atau dikirim ke panitia lomba.

### 5.4 Modul Personal Best Time (PB)
- PB dihitung otomatis per atlet, per nomor lomba (bukan input manual)
- Tabel ringkasan PB seluruh nomor untuk tiap atlet
- **Grafik progres** waktu dari lomba ke lomba (line chart) per nomor lomba
- Badge "PB Baru!" saat atlet mencetak rekor pribadi

### 5.5 Dashboard Admin
- Ringkasan jumlah atlet per kelas (chart)
- Daftar atlet yang baru saja mencetak PB
- Kalender lomba mendatang
- Statistik pertumbuhan jumlah atlet per bulan/tahun
- Log aktivitas (siapa input/edit data terakhir)

### 5.6 Dashboard Orang Tua (User)
- Profil anak (foto, kelas, usia, lama bergabung)
- Riwayat hasil lomba anak
- Grafik perkembangan PB time anak
- Riwayat kenaikan kelas
- Notifikasi lomba mendatang yang diikuti anak

### 5.7 Modul Notifikasi
- Notifikasi in-app & email:
  - Atlet naik kelas
  - Atlet mencetak PB baru
  - Pengingat jadwal lomba
  - Akun user baru menunggu approval (untuk admin)

### 5.8 Modul Laporan
- Export laporan atlet per kelas (PDF/Excel)
- Export leaderboard PB per nomor lomba
- Laporan kehadiran latihan (opsional, pengembangan lanjutan)

### 5.9 Pengaturan Sistem (Super Admin)
- Kelola akun pelatih/admin
- Kelola master data: daftar kelas, daftar gaya & nomor lomba
- Approval registrasi user baru

---

## 6. Struktur Database (ERD Ringkas)

```
users
- id, name, email, password, role (super_admin/admin/user), phone, status

athletes
- id, name, birth_date, gender, photo, current_class_id, join_date, parent_user_id
- ku (INT, generated/derived dari YEAR(birth_date), contoh: 2015)

classes
- id, name (Dasar 0/Dasar 1/Pemula/Pra-Prestasi/Prestasi), description

class_histories
- id, athlete_id, class_id, start_date, end_date, notes, changed_by (admin_id)

competitions   (menyimpan data EVENT, contoh: "Danlanud Cup")
- id, name, organizer, location, start_date, end_date, status (akan_datang/berlangsung/selesai)

competition_events   (nomor lomba yang tersedia dalam suatu competition)
- id, competition_id, stroke (bebas/dada/kupu/punggung/ganti/estafet), distance (meter), category (perorangan/estafet_mix)

competition_entries   (pendaftaran atlet ke suatu event + nomor lomba, dibuat SEBELUM hasil ada)
- id, athlete_id, competition_event_id, status (terdaftar/DNS/DQ)

results   (catatan waktu, diisi SAAT/SETELAH event berlangsung, relasi 1:1 ke entry)
- id, competition_entry_id, time_result, rank, status (finish/DQ/DNS), input_by

personal_bests (view/derived table)
- athlete_id, competition_event_id (khusus nomor perorangan), best_time, achieved_at (dihitung dari tabel results)

notifications
- id, user_id, type, message, is_read, created_at
```

**Relasi kunci:**
- `athletes.parent_user_id` → `users.id` (satu atlet dimiliki satu akun orang tua, satu akun bisa punya banyak atlet)
- `athletes.ku` dihitung otomatis dari `YEAR(birth_date)`, tidak diinput manual, tapi ditampilkan di semua form & export
- `competition_entries` memisahkan proses "pendaftaran nomor lomba" dari "input hasil waktu" — sesuai alur kerja klub: daftar dulu sebelum event, baru isi waktu saat event berlangsung
- `results.competition_entry_id` → dipakai untuk menghitung PB secara otomatis (query `MIN(time_result)` per atlet per nomor lomba perorangan)

---

## 7. Desain UI/UX

**Prinsip desain: Clean & Modern**
- Palet warna: biru laut/turquoise sebagai warna utama (mencerminkan renang/air), putih sebagai base, aksen warna cerah (oranye/kuning) untuk highlight PB/prestasi
- Tipografi: sans-serif modern (misal Inter/Poppins), hierarki jelas antara judul, sub-judul, dan body text
- Layout: sidebar navigasi untuk admin, top navbar sederhana untuk user
- Card-based design untuk profil atlet dan ringkasan data
- Grafik interaktif (hover untuk detail waktu & tanggal lomba)
- Responsive: mobile-friendly khusus untuk halaman orang tua (karena kemungkinan besar diakses via HP)

**Halaman utama yang dibutuhkan:**
1. Login
2. Register (User)
3. Dashboard Admin
4. Daftar Atlet (list + filter)
5. Detail Atlet (profil, riwayat kelas, riwayat lomba, grafik PB)
6. Form Input/Edit Atlet
7. Daftar Event/Perlombaan (Akan Datang, Berlangsung, Selesai)
8. Form Buat Event Baru
9. Halaman Pendaftaran Atlet & Nomor Lomba ke Event (Entry)
10. Halaman Input Catatan Waktu Saat Event Berlangsung
11. Halaman Export Hasil Event ke Excel (Format A1-A3)
12. Dashboard User (Orang Tua)
13. Halaman Notifikasi
14. Halaman Laporan/Export
15. Pengaturan Akun & Sistem (Super Admin)

---

## 8. Alur Pengguna (User Flow)

**Alur Admin (kelola atlet):**
Login → Dashboard Admin → Pilih Atlet/Input Atlet Baru (KU otomatis terhitung dari tahun lahir) → Data Tersimpan

**Alur Admin (kelola event & lomba):**
Login → Buat Event Baru (nama, tanggal, lokasi) → Daftarkan Atlet & Nomor Lomba ke Event → *(saat event berlangsung)* Input Catatan Waktu per Atlet per Nomor → Sistem Update PB Otomatis → Notifikasi Terkirim ke Orang Tua → Export Hasil ke Excel Format A1-A3 untuk diserahkan ke panitia

**Alur Orang Tua:**
Register → Menunggu Approval Admin → Login → Dashboard User → Lihat Profil Anak → Lihat Grafik PB & Riwayat Lomba

---

## 9. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| **Keamanan** | Role-based access control, password hashing (bcrypt), proteksi CSRF bawaan Laravel |
| **Performa** | Query PB dioptimasi dengan index pada `athlete_id` dan `competition_event_id` |
| **Skalabilitas** | Mendukung ratusan atlet dan ribuan hasil lomba tanpa penurunan performa signifikan |
| **Kompatibilitas** | Berjalan baik di browser modern (Chrome, Safari, Edge) dan responsive di mobile |
| **Backup** | Backup database otomatis berkala (harian/mingguan) |

---

## 10. Roadmap Pengembangan (Bertahap)

**Fase 1 — MVP (4–6 minggu)**
- Autentikasi & role
- CRUD Atlet & Kelas
- Input hasil lomba manual
- Perhitungan PB otomatis
- Dashboard admin & user versi dasar

**Fase 2 — Pengembangan Lanjutan (4 minggu)**
- Grafik progres PB
- Notifikasi in-app & email
- Export laporan PDF/Excel
- Riwayat kenaikan kelas

**Fase 3 — Penyempurnaan (opsional, ke depan)**
- Modul kehadiran latihan
- Modul pembayaran/iuran bulanan
- Aplikasi mobile (PWA)

---

## 11. Kesimpulan

Sistem ini dirancang agar pelatih dapat mengambil keputusan berbasis data (misalnya kenaikan kelas) secara objektif, sekaligus memberi orang tua rasa keterlibatan dan transparansi terhadap perkembangan anaknya. Fondasi database yang menyimpan riwayat (bukan hanya data terkini) menjadi kunci agar sistem ini bisa menceritakan **perjalanan** setiap atlet dari waktu ke waktu, bukan sekadar snapshot data.

---

*Dokumen ini adalah draft awal dan dapat disesuaikan lebih lanjut berdasarkan diskusi dengan pihak manajemen Marabunta Swimming Club.*
