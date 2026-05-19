<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Garuda Jaya</title>
    <link rel="icon" type="image/x-icon" href="/images/garuda.ico">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Space Mono', monospace, sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 1rem;
        }

        .login-card {
            background: #ffffff;
            padding: 3rem 2rem 2rem 2rem;
            width: 100%;
            max-width: 380px;
            border: 3px solid #000000;
            border-radius: 12px;
            box-shadow: 8px 8px 0px 0px #000000;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .login-brand {
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: 2px;
            color: #000000;
            margin-bottom: 0.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
        }

        .form-control {
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            border: 2px solid #000000;
            border-radius: 0;
            outline: none;
            transition: all 0.2s;
        }

        .form-control::placeholder {
            color: #888888;
        }

        .form-control:focus {
            box-shadow: 4px 4px 0px 0px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
            background-color: #ff5252;
            border: 2px solid #000000;
            border-radius: 0;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }

        .btn-primary:active {
            transform: translate(2px, 2px);
            box-shadow: none;
        }

        .alert-error {
            background: #fff0f0;
            border: 2px solid #ff5252;
            color: #cc0000;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .signup-text {
            text-align: center;
            font-size: 0.9rem;
            font-weight: 400;
            color: #000000;
            margin-top: 1rem;
        }

        .signup-link {
            font-weight: 700;
            color: #000000;
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        
        .signup-link:hover {
            color: #ff5252;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 0.5rem;">
            <img src="/images/garuda.png" alt="Garuda Jaya Logo" style="height: 64px; width: auto; object-fit: contain; margin-bottom: 0.75rem;">
            <div style="font-size: 1.4rem; font-weight: 800; letter-spacing: 0.5px; color: #111;">Garuda <span style="color: #e53935;">Jaya</span></div>
            <div style="font-size: 0.85rem; color: #777; margin-top: 0.25rem; font-weight: 400;">Selamat datang Silakan masuk ke akun Anda.</div>
        </div>

        {{-- Error Message --}}
        @if(session('error'))
        <div class="alert-error">
            ⚠ {{ session('error') }}
        </div>
        @endif
        
        <form action="{{ route('login.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="username">USERNAME</label>
                <input type="text" id="username" name="username" class="form-control"
                       placeholder="masukkan username"
                       value="{{ old('username') }}"
                       autocomplete="username"
                       required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">PASSWORD</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="........"
                       autocomplete="current-password"
                       required>
            </div>
            
            <button type="submit" class="btn-primary">SIGN IN</button>
        </form>
    </div>
</body>
</html>