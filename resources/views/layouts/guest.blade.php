<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Marabunta Swimming Club - Database Atlet</title>

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/custom-style.css') }}">
    </head>
    <body>
        <div class="guest-container">
            <div class="auth-card">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/logo-msc.png') }}" alt="MSC Logo" 
                         style="width: 56px; height: 56px; object-fit: contain; margin-bottom: 0.65rem; 
                                filter: drop-shadow(0 0 14px rgba(91, 168, 232, 0.4)); border-radius: 0.5rem;">
                    <h1 class="club-title">Marabunta</h1>
                    <span class="club-subtitle">Swimming Club Database</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
