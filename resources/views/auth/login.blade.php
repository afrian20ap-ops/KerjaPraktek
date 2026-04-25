<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Garuda Jaya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: var(--bg-hover); padding: 1rem; }
        .login-card { background: var(--bg-card); padding: 2.5rem; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg); width: 100%; max-width: 420px; border: 1px solid var(--border-color); }
        .login-brand { font-size: 2rem; font-weight: 700; color: var(--primary-500); display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 2rem; }
        .login-brand span { color: var(--text-primary); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <i class="fa-solid fa-fingerprint"></i>
            <span>Garuda <strong style="color:var(--primary-500)">Jaya</strong></span>
        </div>
        <p style="text-align:center;color:var(--text-secondary);margin-bottom:2rem;font-size:0.95rem;">Silakan masuk ke akun Anda.</p>
        
        <form action="#" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email / Username</label>
                <div style="position:relative;">
                    <i class="fa-solid fa-envelope" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
                    <input type="text" id="email" name="email" class="form-control" style="padding-left:2.5rem;" placeholder="Masukkan email..." required>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <div style="display:flex;justify-content:space-between;">
                    <label class="form-label" for="password">Password</label>
                    <a href="#" style="font-size:0.8rem;color:var(--primary-500);font-weight:500;">Lupa Password?</a>
                </div>
                <div style="position:relative;">
                    <i class="fa-solid fa-lock" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
                    <input type="password" id="password" name="password" class="form-control" style="padding-left:2.5rem;" placeholder="Masukkan password..." required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:0.75rem;font-size:1rem;font-weight:600;margin-bottom:1.5rem;">Login Sekarang</button>
        </form>
    </div>
    
    <button class="icon-btn" id="themeToggle" title="Toggle Dark Mode" style="position:fixed;bottom:2rem;right:2rem;background:var(--bg-card);box-shadow:var(--shadow);">
        <i class="fa-solid fa-moon" id="themeIcon"></i>
    </button>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        themeToggle.addEventListener('click', () => {
            const html = document.documentElement;
            if(html.getAttribute('data-theme') === 'dark') {
                html.setAttribute('data-theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            } else {
                html.setAttribute('data-theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }
        });
    </script>
</body>
</html>
