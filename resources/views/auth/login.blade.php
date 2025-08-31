<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PIPA IFRS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pipa-red: #b82424;
            --pipa-red-dark: #9a1d1d;
            --pipa-green: #087c04;
            --pipa-green-dark: #066903;
            --primary-color: #b82424;
            --primary-light: #d86a6a;
            --secondary-color: #087c04;
            --dark-color: #2b2b2b;
            --dark-light: #3d3d3d;
            --light-color: #f8f9fa;
            --text-color: #2b2b2b;
            --card-bg: #ffffff;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        
        [data-bs-theme="dark"] {
            --dark-color: #f8f9fa;
            --dark-light: #e0e0e0;
            --light-color: #2b2b2b;
            --text-color: #f8f9fa;
            --card-bg: #2b2b2b;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: url('{{ asset("images/auth-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .auth-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            border-top: 4px solid var(--pipa-red);
        }

        .auth-logo {
            height: 60px;
            margin-bottom: 20px;
        }

        .auth-title {
            color: var(--text-color);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            color: var(--dark-light);
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .auth-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-text {
            background-color: rgba(184, 36, 36, 0.1);
            border: none;
            color: var(--primary-color);
            padding: 0 15px;
            height: 100%;
            position: absolute;
            z-index: 10;
        }

        .form-control {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: var(--light-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 36, 36, 0.2);
        }

        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 0;
            background: transparent;
            border: none;
            color: var(--dark-light);
            z-index: 10;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0;
            margin-right: 8px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: var(--dark-light);
            font-size: 0.9rem;
        }

        .forgot-password {
            color: var(--primary-color);
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .auth-status {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 8px;
            background-color: rgba(40, 199, 111, 0.1);
            color: var(--success-color);
            font-size: 0.9rem;
        }

        .form-error {
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 6px;
        }

        .auth-footer-text {
            color: var(--dark-light);
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .auth-footer-link {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .auth-footer-link:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .btn-pipa-red {
            background-color: var(--pipa-red);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-pipa-red:hover {
            background-color: var(--pipa-red-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(184, 36, 36, 0.3);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="text-center mb-5">
            <img src="{{ asset('images/logo.png') }}" alt="PIPA IFRS" class="auth-logo">                <h2 class="auth-title">Acesse sua conta</h2>
                <p class="auth-subtitle">Entre para acessar o repositório de jogos educacionais</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="auth-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               required autofocus autocomplete="username" class="form-control" />
                    </div>
                    @if($errors->has('email'))
                        <div class="form-error">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <!-- Password -->
                <div class="form-group mt-4">
                    <label for="password" class="form-label">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" name="password" 
                               required autocomplete="current-password" class="form-control" />
                        <button type="button" class="input-group-text toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @if($errors->has('password'))
                        <div class="form-error">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label">Lembrar-me</label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            Esqueceu sua senha?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-pipa-red btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Entrar
                    </button>
                </div>

                <!-- Register Link -->
                <div class="text-center mt-4">
                    <p class="auth-footer-text">
                        Não tem uma conta? 
                        <a href="{{ route('register') }}" class="auth-footer-link">Registre-se</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelector('.toggle-password');
            const password = document.querySelector('#password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }

            // Theme toggle functionality
            const themeToggle = document.createElement('button');
            themeToggle.className = 'theme-toggle btn btn-sm position-fixed bottom-0 end-0 m-3';
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            themeToggle.onclick = function() {
                const html = document.documentElement;
                const isDark = html.getAttribute('data-bs-theme') === 'dark';
                html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
                this.innerHTML = isDark ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
                localStorage.setItem('darkMode', !isDark);
            };
            
            // Check for saved theme preference
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }
            
            document.body.appendChild(themeToggle);
        });
    </script>
</body>
</html>