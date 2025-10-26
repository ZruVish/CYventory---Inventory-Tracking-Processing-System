<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login - CYventory</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --neon-cyan: #00ffff;
            --neon-pink: #ff0080;
            --neon-purple: #8a2be2;
            --dark-bg: #0a0a0a;
            --dark-surface: #1a1a1a;
            --dark-card: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #888888;
            --border-glow: #333333;
            --success: #00ff88;
            --warning: #ffaa00;
            --danger: #ff4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Orbitron', monospace;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 0, 128, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(138, 43, 226, 0.15) 0%, transparent 50%);
            animation: pulse 6s ease-in-out infinite alternate;
            z-index: -1;
        }

        @keyframes pulse {
            0% { opacity: 0.3; transform: scale(1); }
            100% { opacity: 0.8; transform: scale(1.05); }
        }

        .login-container {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--neon-cyan);
            border-radius: 12px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 
                0 0 50px rgba(0, 255, 255, 0.3),
                inset 0 0 50px rgba(0, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(
                transparent,
                rgba(0, 255, 255, 0.1),
                transparent 30%
            );
            animation: rotate 6s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--neon-cyan), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(0, 255, 255, 0.5);
            margin-bottom: 0.5rem;
        }

        .logo p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--neon-cyan);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            background: var(--dark-surface);
            border: 1px solid var(--border-glow);
            border-radius: 6px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.4);
            background: rgba(26, 26, 26, 0.8);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            border: 1px solid var(--border-glow);
            border-radius: 3px;
            background: var(--dark-surface);
            cursor: pointer;
            position: relative;
        }

        .checkbox:checked {
            background: var(--neon-cyan);
            border-color: var(--neon-cyan);
        }

        .checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--dark-bg);
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, var(--neon-cyan), var(--neon-pink));
            color: var(--dark-bg);
            border: none;
            border-radius: 6px;
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            border-color: var(--success);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }

        .error-text {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid var(--dark-bg);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 2rem;
            }

            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>CYventory</h1>
            <p>Cyberpunk Inventory System</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" 
                       placeholder="Enter your email" 
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" 
                       placeholder="Enter your password" required>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="remember" class="checkbox" id="remember">
                <label class="checkbox-label" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn" id="loginBtn">
                Access System
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            form.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="loading"></span> Authenticating...';
            });

            // Auto-hide alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 300);
                }, 4000);
            });
        });
    </script>
</body>
</html>