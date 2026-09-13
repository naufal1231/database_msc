<x-guest-layout>
    <h2 class="form-title">Masuk ke Sistem</h2>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi (Password)</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" />
        </div>

        <!-- Remember Me -->
        <div class="form-group">
            <label for="remember_me" class="form-checkbox">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            Masuk
        </button>

        <div class="form-footer">
            @if (Route::has('register'))
                <a class="form-link" href="{{ route('register') }}">
                    Daftar Akun
                </a>
            @endif

            @if (Route::has('password.request'))
                <a class="form-link" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
