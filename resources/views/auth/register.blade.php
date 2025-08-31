<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - PIPA IFRS</title>
    
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

        .password-requirements {
            background-color: rgba(0, 0, 0, 0.03);
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--dark-light);
        }

        .requirement-item {
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }

        .requirement-item i {
            color: var(--success-color);
            margin-right: 8px;
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

        .verification-container {
            text-align: center;
        }

        .verification-code-input {
            letter-spacing: 10px;
            font-size: 1.5rem;
            text-align: center;
            font-weight: bold;
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
                <img src="{{ asset('images/logo.png') }}" alt="PIPA IFRS" class="auth-logo">
                <h2 class="auth-title">Crie sua conta</h2>
                <p class="auth-subtitle">Junte-se à comunidade de jogos educacionais</p>
            </div>

            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Formulário de Registro (etapa 1) -->
            <form method="POST" action="{{ route('register') }}" class="auth-form" id="registrationForm">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Nome</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" 
                               required autofocus autocomplete="name" class="form-control" />
                    </div>
                    @if($errors->has('name'))
                        <div class="form-error">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>

                <!-- Email -->
                <div class="form-group mt-4">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               required autocomplete="username" class="form-control" />
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
                               required autocomplete="new-password" class="form-control" />
                        <button type="button" class="input-group-text toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @if($errors->has('password'))
                        <div class="form-error">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="form-group mt-4">
                    <label for="password_confirmation" class="form-label">Confirme a Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password_confirmation" type="password" name="password_confirmation" 
                               required autocomplete="new-password" class="form-control" />
                        <button type="button" class="input-group-text toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="password-requirements mt-3">
                    <p class="mb-2">Sua senha deve conter:</p>
                    <ul class="list-unstyled">
                        <li class="requirement-item"><i class="fas fa-check-circle"></i> Pelo menos 8 caracteres</li>
                        <li class="requirement-item"><i class="fas fa-check-circle"></i> Pelo menos 1 letra maiúscula</li>
                        <li class="requirement-item"><i class="fas fa-check-circle"></i> Pelo menos 1 número</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-pipa-red btn-lg">
                        <i class="fas fa-user-plus me-2"></i> Registrar
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="auth-footer-text">
                        Já tem uma conta? 
                        <a href="{{ route('login') }}" class="auth-footer-link">Faça login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility for both fields
            const toggleButtons = document.querySelectorAll('.toggle-password');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            });

            // Password strength validation
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const requirements = {
                        length: this.value.length >= 8,
                        uppercase: /[A-Z]/.test(this.value),
                        number: /[0-9]/.test(this.value)
                    };
                    
                    // Update requirement indicators
                    document.querySelectorAll('.requirement-item').forEach((item, index) => {
                        const requirement = Object.values(requirements)[index];
                        const icon = item.querySelector('i');
                        icon.style.color = requirement ? 'var(--success-color)' : 'var(--danger-color)';
                    });
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