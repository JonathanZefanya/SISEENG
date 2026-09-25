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
    <?= faviconTag() ?>

    <style>
        body {
            min-height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .login-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        /* ===== Panel brand (atas di mobile, kanan di desktop) ===== */
        .login-panel {
            position: relative;
            overflow: hidden;
            color: #fff;
            text-align: center;
            padding: 3rem 1.5rem 5rem;
            background:
                radial-gradient(circle at 85% 15%, rgba(255, 255, 255, .18), transparent 45%),
                linear-gradient(135deg, var(--brand-light) 0%, var(--brand) 45%, var(--brand-darker) 100%);
        }

        .panel-content { position: relative; z-index: 2; max-width: 320px; margin: 0 auto; }

        .login-logo {
            width: 72px; height: 72px;
            border-radius: 22px;
            background: #fff;
            color: var(--brand);
            display: inline-grid; place-items: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
            margin-bottom: 1.25rem;
        }

        .login-logo .brand-logo-img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }

        .login-panel h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: .5rem; }
        .login-panel p { opacity: .9; margin: 0; line-height: 1.6; }

        /* Awan: lingkaran putih yang menumpuk di tepi panel */
        .cloud {
            position: absolute;
            z-index: 1;
            border-radius: 50%;
            background: #fff;
        }
        .cloud:nth-child(1) { width: 140px; height: 140px; left: -40px;  bottom: -90px; }
        .cloud:nth-child(2) { width: 110px; height: 110px; left: 18%;    bottom: -70px; }
        .cloud:nth-child(3) { width: 160px; height: 160px; left: 38%;    bottom: -115px; }
        .cloud:nth-child(4) { width: 120px; height: 120px; left: 64%;    bottom: -80px; }
        .cloud:nth-child(5) { width: 150px; height: 150px; right: -50px; bottom: -100px; }
        .cloud:nth-child(6) { display: none; }
        .cloud:nth-child(7) { display: none; }

        /* Dekorasi lembut di dalam panel */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            z-index: 0;
        }
        .bubble.b1 { width: 260px; height: 260px; right: -90px; top: -90px; }
        .bubble.b2 { width: 120px; height: 120px; left: -30px; top: 30%; }

        /* ===== Form ===== */
        .login-form {
            flex: 1;
            padding: 1rem 1.5rem 2.5rem;
            position: relative;
            z-index: 2;
        }

        .login-inner { max-width: 360px; margin: 0 auto; }

        .login-head { text-align: center; margin-bottom: 1.75rem; }
        .login-head h2 { font-size: 1.75rem; font-weight: 800; margin-bottom: .25rem; }
        .login-head p { color: var(--muted); margin: 0; font-size: .95rem; }

        .input-pill { position: relative; }
        .input-pill > i {
            position: absolute; left: 1.25rem; top: 50%;
            transform: translateY(-50%);
            color: var(--brand);
            font-size: 1.15rem;
            z-index: 1;
        }
        .input-pill .form-control {
            border: 1px solid transparent;
            border-radius: 999px;
            background: #fff;
            height: 54px;
            padding-left: 3.25rem;
            padding-right: 3.25rem;
            box-shadow: 0 8px 24px rgba(var(--brand-rgb), .12);
        }
        .input-pill .form-control:focus {
            border-color: rgba(var(--brand-rgb), .35);
            box-shadow: 0 8px 24px rgba(var(--brand-rgb), .18), 0 0 0 4px rgba(var(--brand-rgb), .12);
        }
        .input-pill .toggle-pass {
            position: absolute; right: .5rem; top: 50%;
            transform: translateY(-50%);
            border: 0; background: none;
            color: var(--muted);
            width: 40px; height: 40px;
            border-radius: 50%;
        }
        .input-pill .toggle-pass:hover { background: var(--brand-soft); color: var(--brand); }

        .btn-login {
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(var(--brand-rgb), .35);
        }

        /* ===== Desktop: kartu dua kolom, panel di kanan dengan tepi awan ===== */
        @media (min-width: 768px) {
            body {
                justify-content: center;
                align-items: center;
                padding: 2rem;
                background:
                    radial-gradient(circle at 20% 20%, rgba(var(--brand-rgb), .10), transparent 50%),
                    var(--brand-soft);
            }

            .login-card {
                flex: 0 0 auto;
                flex-direction: row;
                width: 100%;
                max-width: 920px;
                min-height: 540px;
                border-radius: 28px;
                overflow: hidden;
                box-shadow: 0 30px 70px rgba(var(--brand-rgb), .25);
            }

            .login-form {
                flex: 1 1 50%;
                display: flex;
                align-items: center;
                padding: 3rem 2.5rem;
            }
            .login-inner { width: 100%; }

            .login-panel {
                order: 2;
                flex: 1 1 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem 2.5rem 3rem 5rem;
            }

            /* Pindahkan awan ke tepi kiri panel */
            .cloud:nth-child(n) { right: auto; bottom: auto; display: block; }
            .cloud:nth-child(1) { width: 150px; height: 150px; left: -95px; top: -50px; }
            .cloud:nth-child(2) { width: 120px; height: 120px; left: -60px; top: 14%; }
            .cloud:nth-child(3) { width: 170px; height: 170px; left: -120px; top: 28%; }
            .cloud:nth-child(4) { width: 130px; height: 130px; left: -70px; top: 50%; }
            .cloud:nth-child(5) { width: 160px; height: 160px; left: -110px; top: 66%; }
            .cloud:nth-child(6) { width: 140px; height: 140px; left: -40px; top: 86%; }
            .cloud:nth-child(7) { width: 120px; height: 120px; left: 22%;  top: 92%; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-panel">
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="bubble b1"></span>
            <span class="bubble b2"></span>

            <div class="panel-content">
                <div class="login-logo"><?= brandMark() ?></div>
                <h1>Pengerja Panel</h1>
                <p>Selamat datang kembali di <?= e(APP_NAME) ?>. Masuk untuk mengelola konten dan jadwal pelayanan.</p>
            </div>
        </div>

        <div class="login-form">
            <div class="login-inner">
                <div class="login-head">
                    <h2>Halo!</h2>
                    <p>Masuk ke akunmu</p>
                </div>

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
                        <label for="email" class="visually-hidden">Email</label>
                        <div class="input-pill">
                            <i class="bi bi-envelope-fill"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="Email" autocomplete="username" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="visually-hidden">Password</label>
                        <div class="input-pill">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Password" autocomplete="current-password" required>
                            <button class="toggle-pass" type="button" id="togglePassword" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-login w-100 mb-3">
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
