# Dokumentasi Sistem Database Atlet - Marabunta Swimming Club (MSC)

Dokumen ini menjelaskan struktur hak akses pengguna (Role), alur kerja (Workflow) utama sistem, dan aturan bisnis yang diterapkan pada aplikasi web "Sistem Database Atlet Marabunta Swimming Club".

---

## 👥 1. Penjelasan Level Pengguna (Role & Otorisasi)

Sistem ini memiliki tiga level pengguna yang dilindungi oleh middleware otorisasi dan kontrol akses yang ketat:

### A. Pelatih Kepala (Super Admin)
* **Tanggung Jawab**: Pemilik sistem dengan kontrol penuh terhadap konfigurasi aplikasi dan manajemen data.
* **Hak Akses Menu**:
  * **Dashboard**: Memantau statistik kumulatif (Jumlah Atlet, Kelas, Event) dan mengelola antrean persetujuan (approve/reject) akun Orang Tua.
  * **Data Atlet**: Melakukan registrasi, pembaruan data, perpindahan kelas (dengan catatan pelatih), pemetaan akun wali, serta penghapusan atlet.
  * **Absensi Harian**: Melakukan pencatatan kehadiran harian atlet per kelas latihan, melihat rekap absensi bulanan, dan mengekspor laporan kehadiran.
  * **Perlombaan**: Membuat event lomba, meng-generate nomor lomba standar, melakukan checklist pendaftaran atlet (entry), menginput catatan waktu, serta mengunduh berkas Excel resmi.
  * **Laporan Kemajuan**: Memfilter rekap performa seluruh atlet dan mencetak dokumen fisik/PDF.
  * **Pengaturan Sistem**: Mengonfigurasi nama klub, alamat kolam, nomor kontak, default nomor lomba, serta menguji status koneksi WhatsApp API Gateway.

### B. Pelatih Kelas (Admin)
* **Tanggung Jawab**: Pelatih pendamping yang bertugas mengelola aktivitas latihan sehari-hari dan hasil lomba atlet.
* **Hak Akses Menu**:
  * **Dashboard**: Memantau statistik dasar dan menyetujui pendaftaran orang tua.
  * **Data Atlet**: Melakukan registrasi dan edit atlet (termasuk memindahkan kelas latihan).
  * **Absensi Harian**: Melakukan pencatatan kehadiran harian atlet per kelas latihan yang dipimpinnya, serta melihat rekap absensi harian per atlet.
  * **Perlombaan**: Mengelola entri perlombaan dan menginput hasil catatan waktu.
  * **Laporan Kemajuan**: Mengakses dan memfilter rekap performa atlet untuk keperluan evaluasi latihan.
  * *Catatan*: Pelatih Kelas tidak memiliki akses ke halaman **Pengaturan Sistem** (Settings).

### C. Orang Tua / Wali Atlet (User)
* **Tanggung Jawab**: Memantau perkembangan latihan dan hasil lomba anak kandungnya secara mandiri.
* **Mekanisme Keamanan (Data Privacy)**: 
  * Rute portal orang tua diproteksi ketat. Query database mencocokkan `parent_user_id` pada tabel `athletes` dengan ID akun orang tua yang sedang login (`auth()->id()`). 
  * Orang tua **tidak dapat mengakses** data pribadi, catatan waktu, atau grafik progres atlet lain.
* **Hak Akses Portal**:
  * **Informasi Profil Anak**: Melihat data detail anak, kategori JK (PA/PI), dan kelas latihan aktif saat ini.
  * **Tab Navigasi Multi-Anak**: Jika orang tua memiliki lebih dari satu anak di klub, dashboard menyajikan tab untuk mempermudah perpindahan data anak.
  * **Tabel Personal Best (PB)**: Daftar catatan waktu tercepat anak untuk masing-masing nomor lomba yang pernah diikuti.
  * **Grafik Progres (Chart.js)**: Grafik garis interaktif yang melacak progres catatan waktu anak secara kronologis berdasarkan nomor lomba yang dipilih dari dropdown.
  * **Riwayat Kenaikan Kelas**: Log histori kenaikan jenjang kelas latihan anak yang mencantumkan catatan progres dari pelatih.
  * **Notifikasi Terbaru**: Feed aktivitas terbaru yang menginfokan kenaikan kelas anak atau hasil waktu lomba baru yang di-input pelatih.
  * **Riwayat Absensi**: Melihat histori kehadiran anak (izin, sakit, tanpa keterangan) per bulan dalam format tabel dan grafik.

---

## 🔄 2. Alur Kerja Utama Sistem (Workflows)

### A. Alur Pendaftaran & Persetujuan Akun Orang Tua
1. **Registrasi**: Orang Tua mendaftar melalui form registrasi umum dengan menginput Nama, Email, Nomor Telepon/WhatsApp, dan Password.
2. **Status Pending**: Secara default, akun baru terdaftar dengan `role => 'user'` dan `status => 'pending'`.
3. **Mencegat Akses**: Saat login pertama kali, akun pending dicegah masuk ke dashboard orang tua oleh middleware `EnsureUserIsActive` dan otomatis dialihkan ke halaman `/waiting-approval`.
4. **Verifikasi Pelatih**: Pelatih masuk ke Dashboard Admin, melihat pendaftaran baru pada tabel **Persetujuan Akun Orang Tua**, lalu meninjau kontak WhatsApp pendaftar.
5. **Approve/Reject**:
   * Jika **Disetujui**: Pelatih mengklik "Setujui". Status akun berubah menjadi `'active'`.
   * Jika **Ditolak**: Pelatih mengklik "Tolak". Akun akan dihapus dari database.
6. **Portal Terbuka**: Pada login berikutnya, orang tua yang aktif langsung diarahkan masuk ke Dashboard Orang Tua.

### B. Alur CRUD Atlet & Pelacakan Histori Kelas Latihan
1. **Pendaftaran Atlet**: Pelatih mendaftarkan atlet baru dengan menginput Nama, Tanggal Lahir (Kelompok Umur/KU dihitung otomatis oleh database MySQL dari tahun kelahiran), Jenis Kelamin (`L` dipetakan ke **PA/Putera**; `P` dipetakan ke **PI/Puteri**), foto atlet, dan kelas latihan awal.
2. **Histori Awal**: Sistem secara otomatis mencatat baris baru pada tabel `class_histories` sebagai log masuk kelas pertama kali.
3. **Kenaikan/Perpindahan Kelas**:
   * Di form edit atlet, jika pelatih mengubah pilihan kelas latihan, JavaScript akan memicu tampilan textarea **Catatan Perpindahan Kelas** (Wajib diisi).
   * Pada saat form disimpan, sistem secara otomatis mengisi tanggal selesai (`end_date` => hari ini) pada histori kelas yang lama, lalu membuat baris histori kelas baru dengan catatan kemajuan yang ditulis pelatih.
   * Sistem otomatis memicu pengiriman notifikasi ke akun orang tua yang bersangkutan.

### C. Alur Manajemen Event & Hasil Lomba Renang
1. **Pembuatan Event**: Pelatih membuat event perlombaan baru (Nama, Penyelenggara, Lokasi, dan Tanggal).
2. **Nomor Lomba Standar**: Jika opsi "Otomatis Buat Nomor Lomba Standar Klub" dicentang, sistem langsung membuat 11 nomor lomba renang standar internal MSC (Gaya Bebas, Dada, Kupu, Punggung, Ganti, dan Estafet dengan berbagai jarak).
3. **Pendaftaran Nomor Lomba (Entry)**:
   * Pelatih membuka halaman **Daftarkan Atlet (Entry)**.
   * Sistem menampilkan matriks checkbox antara Atlet (Baris) dan Nomor Lomba (Kolom), dilengkapi filter Kelas dan Kelompok Umur (KU) untuk mempermudah pendaftaran massal.
   * Ceklist dicentang ➡️ Atlet didaftarkan. Ceklist dilepas ➡️ Entri dihapus secara aman dari database.
4. **Input Catatan Waktu**:
   * Pelatih membuka halaman **Input Catatan Waktu** dan memilih nomor lomba.
   * Untuk menginput catatan waktu, sistem menyediakan JavaScript Input Mask kustom yang otomatis memformat pengetikan ke standar `MM.SS.CC` (Menit.Detik.Centiseconds / contoh: mengetik `003294` menjadi `00.32.94`).
   * **Validasi Bersyarat**:
     * Jika status atlet **Finish**: Waktu wajib diisi dan harus lolos validasi Regex `/^\d{2}\.\d{2}\.\d{2}$/`. Peringkat wajib diisi.
     * Jika status **DNS** (No Show) atau **DQ** (Disqualified): Kolom input waktu otomatis terkunci (di-disable), validasi dilewati, dan waktu disimpan sebagai `00.00.00`.
   * Sistem otomatis memicu penghitungan peringkat dan mengirim notifikasi hasil lomba ke akun orang tua atlet.

### D. Alur Ekspor Laporan & Cetak Fisik
1. **Ekspor Excel Resmi**:
   * Pada halaman detail event, terdapat panel hijau khusus berisi 3 opsi ekspor Excel resmi.
   * **Form A1**: Hasil nomor perorangan Gaya Bebas, Dada, Kupu, dan Punggung.
   * **Form A2**: Hasil nomor perorangan Gaya Ganti (Medley).
   * **Form A3**: Hasil nomor Estafet / Estafet Mix.
   * Unduhan Excel menggunakan nama file otomatis standar resmi klub: `[Kode Form] [Nama Event].xlsx`.
2. **Cetak Rekap Kemajuan Atlet**:
   * Pelatih membuka menu **Laporan Kemajuan**, memilih filter (Kelas Latihan, KU, Gender), lalu mengklik "Cetak Laporan".
   * Sistem membuka tab baru dengan pratinjau halaman cetak bersih (monokromatik, tanpa sidebar/navbar) didukung CSS `@media print` dan langsung membuka kotak dialog cetak browser (`window.print()`).

### E. Alur Absensi Harian Atlet
1. **Pencatatan Kehadiran**:
   * Pelatih membuka menu **Absensi Harian**, memilih kelas latihan, lalu melihat daftar atlet yang terdaftar di kelas tersebut.
   * Untuk setiap atlet, pelatih memilih status kehadiran: **Hadir**, **Izin**, **Sakit**, atau **Tanpa Keterangan**.
   * Sistem mencatat status kehadiran beserta timestamp, nama pelatih yang mencatat, dan kelas latihan.
   * Jika pelatih memilih **Izin** atau **Sakit**, sistem menampilkan textarea opsional untuk catatan (misalnya: surat keterangan, foto bukti, dsb).
   * Pencatatan absensi bersifat harian - satu atlet hanya bisa dicatat satu kali per hari.
2. **Rekap Absensi**:
   * Pelatih dapat melihat rekap absensi per hari, per minggu, atau per bulan dengan filter kelas latihan dan kelompok umur.
   * Tampilan rekap menampilkan persentase kehadiran total, jumlah hadir/izin/sakit/tanpa keterangan, serta daftar lengkap kehadiran per atlet dalam bentuk tabel.
3. **Ekspor Laporan Absensi**:
   * Pada halaman rekap, pelatih dapat mengekspor data ke format Excel untuk keperluan administrasi klub atau laporan ke pengelola.
   * Nama file ekspor otomatis mengikuti format: `[Kelas] [Bulan] [Tahun] - Absensi.xlsx`.
4. **Akses Orang Tua**:
  * Orang tua dapat melihat histori absensi anaknya dengan filter bulan dan tahun.
  * Tampilan menampilkan:
    * Tabel hari per hari dengan status kehadiran
    * Ringkasan: jumlah hadir, izin, sakit, tanpa keterangan
    * Grafik pie chart persentase kehadiran bulanan
  * Notifikasi internal (feed aktivitas) dikirim ke akun orang tua jika anak absen tanpa keterangan.

---

## 🛠️ 3. Aturan Teknis Tambahan (Business Rules)

* **Kelompok Umur (KU)**: Kolom `ku` pada database dihitung secara otomatis menggunakan constraint MySQL `YEAR(birth_date)`. Di sisi aplikasi Laravel, setelah menyimpan objek `Athlete`, Anda harus memanggil `$athlete->refresh()` untuk membaca nilai `ku` yang baru di-generate di memori PHP.
* **SwimClass**: Di level kode PHP, model untuk representasi kelas renang dinamakan `SwimClass` dikarenakan kata kunci `Class` adalah *reserved keyword* yang terlarang didefinisikan sebagai nama kelas PHP. Tabel database-nya tetap menggunakan `classes`.
* **Konversi Waktu**: Perbandingan waktu tercepat (Personal Best) dan plotting titik grafik linear Chart.js memanfaatkan helper `TimeHelper` (`app/Helpers/TimeHelper.php`) untuk melakukan konversi timbal-balik dari string format `MM.SS.CC` menjadi nilai desimal centiseconds (angka murni), sehingga pengurutan data di sumbu Y grafik berjalan akurat.
* **Tabel Absensi (`attendances`)**: 
  * Struktur: `id`, `athlete_id` (FK), `swim_class_id` (FK), `attendance_date`, `status` (enum: `hadir`, `izin`, `sakit`, `tanpa_keterangan`), `note` (text, nullable), `created_by` (FK users), `created_at`, `updated_at`.
  * Validasi: Satu atlet hanya dapat memiliki satu catatan absensi per hari (`unique` constraint pada kombinasi `athlete_id` + `attendance_date`).
  * Timestamp: Field `created_at` otomatis mencatat nama pelatih yang melakukan pencatatan.
* **Relasi Model**:
  * `Attendance` belongsTo `Athlete`
  * `Attendance` belongsTo `SwimClass`
  * `Attendance` belongsTo `User` (pelatih pencatat)
  * `Athlete` hasMany `Attendance`
* **Rekap Absensi**:
  * Persentase kehadiran dihitung sebagai: `(Jumlah Hadir / Total Hari Kerja) * 100%`.
  * Hari kerja dihitung sebagai jumlah hari dalam periode filter dikurangi hari libur weekend (Sabtu-Minggu).
* **Statistik Dashboard**:
  * Persentase kehadiran bulanan atlet ditampilkan di dashboard admin dan orang tua.
  * Pelatih dapat melihat statistik kehadiran per kelas untuk identifikasi kelas dengan tingkat kehadiran rendah.

---

## 📝 4. Contoh Struktur Database & Kode

### Migration `create_attendances_table`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('swim_class_id')->constrained('classes')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'tanpa_keterangan']);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['athlete_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
```

### Model `Attendance`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'athlete_id', 
        'swim_class_id', 
        'attendance_date', 
        'status', 
        'note', 
        'created_by'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function swimClass(): BelongsTo
    {
        return $this->belongsTo(SwimClass::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

### Model `Athlete` (tambahan relasi):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Athlete extends Model
{
    // ... existing code
    
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function getMonthlyAttendancePercentage(int $month, int $year): float
    {
        $workDays = $this->calculateWorkDays($month, $year);
        $presentCount = $this->attendances()
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'hadir')
            ->count();
            
        return $workDays > 0 ? ($presentCount / $workDays) * 100 : 0;
    }
    
    private function calculateWorkDays(int $month, int $year): int
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $weekendCount = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $timestamp = mktime(0, 0, 0, $month, $day, $year);
            if (date('N', $timestamp) >= 6) { // 6=Saturday, 7=Sunday
                $weekendCount++;
            }
        }
        
        return $daysInMonth - $weekendCount;
    }
}
```

### Controller `AttendanceController` (metode rekap):
```php
<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SwimClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classes = SwimClass::all();
        $selectedClass = $request->get('swim_class_id');
        $selectedDate = $request->get('date', Carbon::today()->toDateString());
        
        $attendances = Attendance::with(['athlete', 'createdBy'])
            ->when($selectedClass, function ($query) use ($selectedClass) {
                return $query->where('swim_class_id', $selectedClass);
            })
            ->whereDate('attendance_date', $selectedDate)
            ->get();
            
        return view('admin.attendance.index', compact('classes', 'selectedClass', 'selectedDate', 'attendances'));
    }
    
    public function recap(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $classId = $request->get('swim_class_id');
        
        $attendances = Attendance::with('athlete')
            ->when($classId, function ($query) use ($classId) {
                return $query->where('swim_class_id', $classId);
            })
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get()
            ->groupBy('athlete_id');
            
        $summary = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'hadir')->count(),
            'permission' => $attendances->where('status', 'izin')->count(),
            'sick' => $attendances->where('status', 'sakit')->count(),
            'absent' => $attendances->where('status', 'tanpa_keterangan')->count(),
        ];
        
        return view('admin.attendance.recap', compact('attendances', 'summary', 'month', 'year'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'swim_class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,tanpa_keterangan',
            'note' => 'nullable|string|max:500',
        ]);
        
        // Check if attendance already exists for this athlete on this date
        $existing = Attendance::where('athlete_id', $request->athlete_id)
            ->whereDate('attendance_date', $request->attendance_date)
            ->first();
            
        if ($existing) {
            return back()->withErrors(['attendance' => 'Absensi untuk atlet ini sudah dicatat hari ini.']);
        }
        
        Attendance::create([
            'athlete_id' => $request->athlete_id,
            'swim_class_id' => $request->swim_class_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Absensi berhasil dicatat.');
    }
}
```

### Route untuk Absensi:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin|super-admin'])->group(function () {
        Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/absensi', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/absensi/rekap', [AttendanceController::class, 'recap'])->name('attendance.recap');
        Route::get('/absensi/export/{month}/{year}/{class?}', [AttendanceController::class, 'export'])
            ->name('attendance.export');
    });
    
    // Parent routes
    Route::middleware(['role:user'])->group(function () {
        Route::get('/orang-tua/absensi', [ParentController::class, 'attendanceHistory'])
            ->name('parent.attendance');
        Route::get('/orang-tua/absensi/{month}/{year}', [ParentController::class, 'attendanceMonthly'])
            ->name('parent.attendance.monthly');
    });
});
```

### Route untuk Absensi:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin|super-admin'])->group(function () {
        Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/absensi', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/absensi/rekap', [AttendanceController::class, 'recap'])->name('attendance.recap');
        Route::get('/absensi/export/{month}/{year}/{class?}', [AttendanceController::class, 'export'])
            ->name('attendance.export');
    });
    
    // Parent routes
    Route::middleware(['role:user'])->group(function () {
        Route::get('/orang-tua/absensi', [ParentController::class, 'attendanceHistory'])
            ->name('parent.attendance');
        Route::get('/orang-tua/absensi/{month}/{year}', [ParentController::class, 'attendanceMonthly'])
            ->name('parent.attendance.monthly');
    });
});
```

### Dashboard Widget untuk Orang Tua (di portal orang tua):
```php
<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ParentController extends Controller
{
    public function attendanceHistory()
    {
        $athletes = auth()->user()->children; // assuming relation defined
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $attendanceData = [];
        foreach ($athletes as $athlete) {
            $attendanceData[$athlete->id] = [
                'name' => $athlete->name,
                'attendance_percentage' => $athlete->getMonthlyAttendancePercentage($currentMonth, $currentYear),
                'attendance_this_month' => $athlete->attendances()
                    ->whereMonth('attendance_date', $currentMonth)
                    ->whereYear('attendance_date', $currentYear)
                    ->orderBy('attendance_date', 'desc')
                    ->take(10)
                    ->get(),
            ];
        }
        
        return view('parent.attendance.history', compact('athletes', 'attendanceData'));
    }
}
```

---

## ✅ 5. Implementasi Checklist

1. **Database**:
   - [ ] Buat migration `create_attendances_table`
   - [ ] Jalankan migration

2. **Models**:
   - [ ] Buat model `Attendance`
   - [ ] Update model `Athlete` dengan relasi `attendances()`
   - [ ] Update model `User` dengan relasi `children()` untuk orang tua

3. **Controllers**:
   - [ ] Buat `AttendanceController`
   - [ ] Buat `ParentController` (atau update yang ada)

4. **Views**:
   - [ ] Buat view `admin/attendance/index.blade.php` (input absensi harian)
   - [ ] Buat view `admin/attendance/recap.blade.php` (rekap bulanan)
   - [ ] Buat view `parent/attendance/history.blade.php` (riwayat absensi anak)
   - [ ] Update dashboard admin untuk menampilkan statistik kehadiran

5. **Routes**:
   - [ ] Tambahkan routes untuk fitur absensi

6. **Features**:
   - [ ] Implementasi pencatatan absensi harian
   - [ ] Implementasi rekap absensi (per hari, minggu, bulan)
   - [ ] Implementasi ekspor Excel
   - [ ] Implementasi widget absensi di dashboard orang tua
   - [ ] Implementasi notifikasi internal (feed aktivitas) untuk absensi

7. **Testing**:
   - [ ] Test pencatatan absensi
   - [ ] Test validasi (unique per hari)
   - [ ] Test ekspor Excel
   - [ ] Test tampilan untuk orang tua

---

Dengan fitur absensi harian ini, sistem Marabunta Swimming Club menjadi lebih lengkap untuk memantau kehadiran atlet secara berkala dan memberikan transparansi kepada orang tua tentang perkembangan kehadiran anak mereka.
