<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 100vh;
        }
        
        .login-card {
            max-width: 420px;
            width: 100%;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-primary {
            padding: 12px 24px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div class="login-card">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <!-- Logo/Header -->
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-brightness-high-fill fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-primary">Admin Panel</h4>
                    <p class="text-muted"><?= e(APP_NAME) ?></p>
                </div>
                
                <!-- Flash Messages -->
                <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?= getFlash('error') ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (hasFlash('success')): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div><?= getFlash('success') ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['expired'])): ?>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-clock-history me-2"></i>
                    <div>Sesi Anda telah berakhir. Silakan login kembali.</div>
                </div>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form action="<?= url('auth/proses-login') ?>" method="POST">
                    <?= csrfField() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1"></i>Email
                        </label>
                        <input type="email" 
                               class="form-control form-control-lg" 
                               id="email" 
                               name="email" 
                               placeholder="admin@gereja.com"
                               required 
                               autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">
                            <i class="bi bi-lock me-1"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>
                
                <div class="text-center">
                    <a href="<?= url() ?>" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Website
                    </a>
                </div>
            </div>
        </div>
        
        <p class="text-center text-white-50 mt-4 small">
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>
        </p>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
