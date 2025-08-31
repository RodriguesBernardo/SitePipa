<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Email - PIPA IFRS</title>
    
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
        
        .verification-container {
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

        .verification-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            border-top: 4px solid var(--pipa-red);
            text-align: center;
        }

        .verification-logo {
            height: 60px;
            margin-bottom: 20px;
        }

        .verification-title {
            color: var(--text-color);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .verification-subtitle {
            color: var(--dark-light);
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .verification-code-input {
            letter-spacing: 15px;
            font-size: 1.8rem;
            text-align: center;
            font-weight: bold;
            padding: 15px;
            height: 70px;
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            background-color: var(--light-color);
            color: var(--text-color);
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

        @media (max-width: 576px) {
            .verification-card {
                padding: 30px 20px;
            }
            
            .verification-code-input {
                font-size: 1.5rem;
                letter-spacing: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <!-- Logo -->
            <div class="text-center mb-5">
                <img src="{{ asset('images/logo.png') }}" alt="PIPA IFRS" class="verification-logo">
                <h2 class="verification-title">Verifique seu Email</h2>
                <p class="verification-subtitle">
                    Enviamos um código de verificação de 6 dígitos para<br>
                    <strong>{{ $email }}</strong>
                </p>
            </div>

            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div class="mb-4">
                    <label for="code" class="form-label">Digite o código de verificação</label>
                    <input type="text" id="code" name="code" class="form-control verification-code-input" 
                           maxlength="6" required autofocus pattern="[A-Za-z0-9]{6}">
                    @if($errors->has('code'))
                        <div class="text-danger mt-2">{{ $errors->first('code') }}</div>
                    @endif
                </div>
                
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-pipa-red btn-lg">
                        <i class="fas fa-check-circle me-2"></i> Verificar Email
                    </button>
                </div>
                
                <p class="auth-footer-text">
                    Não recebeu o código? 
                    <a href="{{ route('verification.resend', ['email' => $email]) }}" class="auth-footer-link">Reenviar código</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('code');
            
            // Auto-move focus and auto-uppercase
            codeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
                
                if (this.value.length === 6) {
                    this.form.submit();
                }
            });
            
            // Allow only alphanumeric characters
            codeInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.keyCode || e.which);
                if (!/^[A-Za-z0-9]*$/.test(char)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>