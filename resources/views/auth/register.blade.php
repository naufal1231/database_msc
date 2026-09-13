<x-guest-layout>
    <h2 class="form-title">Daftar Akun Orang Tua</h2>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style-type: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap Orang Tua</label>
            <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
        </div>

        <!-- Phone Number -->
        <div class="form-group">
            <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
            <input id="phone" class="form-input" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 08123456789" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi (Password)</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <button type="submit" class="btn btn-primary">
            Daftar
        </button>

        <div class="form-footer" style="justify-content: center;">
            <a class="form-link" href="{{ route('login') }}">
                Sudah memiliki akun? Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
