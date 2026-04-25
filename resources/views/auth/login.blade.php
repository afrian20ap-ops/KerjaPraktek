<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            background-color: #ff5252; /* Match the red/pink in image */
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

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #000000;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 2px solid #000000;
        }

        .divider::before {
            margin-right: 1rem;
        }

        .divider::after {
            margin-left: 1rem;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-btn {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: #000000;
            background: #ffffff;
            border: 2px solid #000000;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border-radius: 0;
        }

        .social-btn:active {
            transform: translate(2px, 2px);
            background: #f0f0f0;
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
        <div class="login-brand">LOGIN</div>
        
        <form action="{{ route('login.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">EMAIL</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">PASSWORD</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="........" required>
            </div>
            
            <button type="submit" class="btn-primary">SIGN IN</button>
        </form>
        
        <div class="divider">OR</div>
        
        <div class="social-login">
            <button type="button" class="social-btn">G</button>
            <button type="button" class="social-btn">F</button>
            <button type="button" class="social-btn">X</button>
        </div>
        
        <p class="signup-text">
            Don't have an account? <a href="#" class="signup-link">Sign up</a>
        </p>
    </div>
</body>
</html>
