<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="<?= themeColor() ?>">
    <title><?= e($title ?? 'Login') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <?= themeStyleTag() ?>

    <style>
        body {
            min-height: 100vh;
            background: var(--brand);
            display: flex;
            flex-direction: column;
        }

        .login-top {
            color: #fff;
            text-align: center;
            padding: 3rem 1.5rem 4.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-top::after {
            content: '';
            position: absolute;
            right: -100px; top: -100px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .login-logo {
            width: 72px; height: 72px;
            border-radius: 22px;
            background: #fff;
            color: var(--brand);
            display: inline-grid; place-items: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
            margin-bottom: 1rem;
        }

        .login-top h1 { font-size: 1.6rem; margin-bottom: .25rem; }
        .login-top p { opacity: .9; margin: 0; }

        .login-sheet {
            flex: 1;
            background: #fff;
            border-radius: 28px 28px 0 0;
            margin-top: -2.5rem;
            padding: 2rem 1.5rem 2.5rem;
            position: relative;
            z-index: 1;
        }

        .login-inner { max-width: 400px; margin: 0 auto; }

        .input-icon { position: relative; }
        .input-icon > i {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 1.1rem;
        }
        .input-icon .form-control { padding-left: 2.8rem; padding-right: 3rem; }
        .input-icon .toggle-pass {
            position: absolute; right: .5rem; top: 50%;
            transform: translateY(-50%);
            border: 0; background: none;
            color: var(--muted);
            width: 40px; height: 40px;
            border-radius: 50%;
        }
        .input-icon .toggle-pass:hover { background: var(--canvas); }

        /* Desktop: kartu mengambang di tengah */
        @media (min-width: 768px) {
            body { justify-content: center; align-items: center; padding: 2rem; }
            .login-wrap {
                width: 100%;
                max-width: 440px;
                border-radius: 28px;
                overflow: hidden;
                box-shadow: 0 30px 60px rgba(0, 0, 0, .2);
                background: #fff;
            }
            .login-top { background: var(--brand); }
            .login-sheet { border-radius: 28px 28px 0 0; }
        }
    </style>
</head>
<body>
    <div class="login-wrap d-flex flex-column flex-grow-1 flex-md-grow-0">
        <div class="login-top">
            <div class="login-logo"><i class="bi bi-brightness-high-fill"></i></div>
            <h1>Pengerja Panel</h1>
            <p><?= e(APP_NAME) ?></p>
        </div>

        <div class="login-sheet">
            <div class="login-inner">
                <h2 class="h5 fw-bold mb-1">Masuk ke akunmu</h2>
                <p class="text-muted small mb-4">Gunakan email dan password yang diberikan.</p>

                <?php if (hasFlash('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div><?= getFlash('error') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (hasFlash('success')): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div><?= getFlash('success') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['expired'])): ?>
                    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-clock-history"></i>
                        <div>Sesi Anda telah berakhir. Silakan login kembali.</div>
                    </div>
                <?php endif; ?>

                <form action="<?= url('auth/proses-login') ?>" method="POST">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-icon">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="admin@gereja.com" autocomplete="username" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="••••••••" autocomplete="current-password" required>
                            <button class="toggle-pass" type="button" id="togglePassword" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                        Masuk
                    </button>
                </form>

                <div class="text-center">
                    <a href="<?= url() ?>" class="small fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Website
                    </a>
                </div>

                <p class="text-center text-muted small mt-4 mb-0">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !show);
            icon.classList.toggle('bi-eye-slash', show);
        });
    </script>
</body>
</html>
