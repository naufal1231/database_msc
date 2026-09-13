<x-guest-layout>
    <div class="waiting-screen">
        <div class="waiting-icon">⏳</div>
        <h2 class="waiting-title">Menunggu Verifikasi</h2>
        <p class="waiting-text">
            Pendaftaran berhasil! Akun Anda saat ini berstatus <strong>Menunggu Verifikasi</strong> dari Pelatih Kepala (Super Admin) Marabunta Swimming Club demi keamanan data atlet.<br><br>
            Silakan hubungi pelatih kepala atau administrator untuk mengaktifkan akun Anda.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-accent">
                Keluar (Log Out)
            </button>
        </form>
    </div>
</x-guest-layout>
