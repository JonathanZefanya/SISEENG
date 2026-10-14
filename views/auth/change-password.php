<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="<?= themeColor() ?>">
    <title><?= e($title ?? 'Ganti Password') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <?= themeStyleTag() ?>
    <?= faviconTag() ?>

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            background:
                radial-gradient(circle at 20% 20%, rgba(var(--brand-rgb), .10), transparent 50%),
                var(--brand-soft);
        }

        .pass-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 28px;
            padding: 2.5rem 1.75rem;
            box-shadow: 0 30px 70px rgba(var(--brand-rgb), .2);
        }

        .pass-icon {
            position: relative;
            width: 64px; height: 64px;
            border-radius: 20px;
            display: grid; place-items: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
            color: #fff;
            background: linear-gradient(135deg, var(--brand-light) 0%, var(--brand) 50%, var(--brand-darker) 100%);
        }

        .pass-head { text-align: center; margin-bottom: 1.5rem; }
        .pass-head h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: .25rem; }
        .pass-head p { color: var(--muted); margin: 0; font-size: .95rem; }

        .input-pill { position: relative; }
        .input-pill > i {
            position: absolute; left: 1.25rem; top: 50%;
            transform: translateY(-50%);
            color: var(--brand);
            font-size: 1.15rem;
            z-index: 1;
            transition: transform .25s cubic-bezier(.34, 1.56, .64, 1);
        }
        .input-pill:focus-within > i { transform: translateY(-50%) scale(1.25) rotate(-8deg); }
        .input-pill .form-control {
            transition: border-color .2s, box-shadow .2s;
            border: 1px solid transparent;
            border-radius: 999px;
            background: #fff;
            height: 52px;
            padding-left: 3.25rem;
            box-shadow: 0 8px 24px rgba(var(--brand-rgb), .12);
        }
        .input-pill .form-control:focus {
            border-color: rgba(var(--brand-rgb), .35);
            box-shadow: 0 8px 24px rgba(var(--brand-rgb), .18), 0 0 0 4px rgba(var(--brand-rgb), .12);
        }

        .btn-pill {
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(var(--brand-rgb), .35);
            transition: transform .2s, box-shadow .2s;
        }
        .btn-pill:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(var(--brand-rgb), .4); }
        .btn-pill:active { transform: translateY(0); }

        /* Indikator konfirmasi password */
        .match-hint {
            font-size: .8rem;
            margin-top: .35rem;
            min-height: 1.2em;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity .2s, transform .2s;
        }
        .match-hint.show { opacity: 1; transform: none; }
        .match-hint.ok { color: #198754; }
        .match-hint.bad { color: #dc3545; }

        /* Dekorasi latar */
        .bubble {
            position: fixed;
            z-index: 0;
            border-radius: 50%;
            background: rgba(var(--brand-rgb), .08);
            animation: bubbleFloat 9s ease-in-out infinite alternate;
        }
        .bubble.b1 { width: 320px; height: 320px; right: -110px; top: -110px; }
        .bubble.b2 { width: 180px; height: 180px; left: -60px; bottom: 10%; animation-duration: 7s; animation-delay: -3s; }

        /* ===== Animasi masuk ===== */
        .pass-card { animation: cardIn .7s cubic-bezier(.2, .8, .2, 1) backwards; }
        .pass-icon { animation: iconPop .8s cubic-bezier(.34, 1.56, .64, 1) .25s backwards; }
        .pass-icon > i { display: inline-block; animation: lockWiggle .7s ease-in-out 1.05s; }
        .pass-icon::after {
            content: "";
            position: absolute; inset: 0;
            border-radius: inherit;
            box-shadow: 0 0 0 0 rgba(var(--brand-rgb), .45);
            animation: pulseRing 2.4s ease-out 1.6s infinite;
        }
        .reveal { animation: fadeUp .55s cubic-bezier(.2, .8, .2, 1) backwards; animation-delay: calc(.4s + var(--i, 0) * .08s); }
        .alert-danger.reveal {
            animation: fadeUp .55s cubic-bezier(.2, .8, .2, 1) backwards, shake .45s ease;
            animation-delay: calc(.4s + var(--i, 0) * .08s), .9s;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(.96); }
            to { opacity: 1; transform: none; }
        }
        @keyframes iconPop {
            0% { opacity: 0; transform: translateY(-24px) scale(.4) rotate(-20deg); }
            60% { opacity: 1; transform: translateY(4px) scale(1.08) rotate(4deg); }
            100% { opacity: 1; transform: none; }
        }
        @keyframes lockWiggle {
            0%, 100% { transform: rotate(0); }
            20% { transform: rotate(-14deg); }
            40% { transform: rotate(10deg); }
            60% { transform: rotate(-6deg); }
            80% { transform: rotate(3deg); }
        }
        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(var(--brand-rgb), .45); }
            70%, 100% { box-shadow: 0 0 0 14px rgba(var(--brand-rgb), 0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: none; }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        @keyframes bubbleFloat {
            from { transform: translate(0, 0); }
            to { transform: translate(-18px, 22px); }
        }

        /* ===== Modal tunda ganti password ===== */
        .modal-backdrop.show { opacity: .45; }
        .modal-backdrop { backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }

        #skipModal .modal-dialog { max-width: 420px; }
        #skipModal.fade .modal-dialog {
            transform: translateY(30px) scale(.88);
            transition: transform .45s cubic-bezier(.34, 1.56, .64, 1), opacity .3s;
            opacity: 0;
        }
        #skipModal.show .modal-dialog { transform: none; opacity: 1; }

        .skip-content {
            border: 0;
            border-radius: 28px;
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(0, 0, 0, .25);
        }
        .skip-content::before {
            content: "";
            position: absolute; inset: 0 0 auto 0;
            height: 110px;
            background: radial-gradient(circle at 50% 0%, rgba(255, 193, 7, .22), transparent 70%);
            pointer-events: none;
        }
        .skip-content .btn-close { position: absolute; top: 1rem; right: 1rem; z-index: 2; }

        .warn-icon {
            position: relative;
            width: 72px; height: 72px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: grid; place-items: center;
            font-size: 2rem;
            color: #fff;
            background: linear-gradient(135deg, #ffd166 0%, #f59e0b 60%, #d97706 100%);
            box-shadow: 0 12px 28px rgba(245, 158, 11, .4);
        }
        .warn-icon::after {
            content: "";
            position: absolute; inset: 0;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, .5);
        }
        .skip-content h2 { font-size: 1.35rem; font-weight: 800; margin-bottom: .35rem; }
        .skip-lead { color: var(--muted); font-size: .92rem; margin-bottom: 1.25rem; }

        .risk-list { list-style: none; padding: 0; margin: 0 0 1rem; text-align: left; }
        .risk-item {
            display: flex; align-items: flex-start; gap: .85rem;
            padding: .75rem .85rem;
            border-radius: 16px;
            background: #fff7f7;
            border: 1px solid #fde2e2;
            font-size: .875rem;
            line-height: 1.45;
        }
        .risk-item + .risk-item { margin-top: .5rem; }
        .risk-item strong { display: block; font-size: .9rem; }
        .risk-item span { color: var(--muted); }
        .risk-icon {
            flex: 0 0 36px;
            width: 36px; height: 36px;
            border-radius: 12px;
            display: grid; place-items: center;
            color: #dc3545;
            background: #fde2e2;
            font-size: 1.05rem;
        }

        .skip-note {
            display: flex; align-items: center; gap: .6rem;
            text-align: left;
            font-size: .8rem;
            color: var(--muted);
            background: var(--brand-soft);
            border-radius: 14px;
            padding: .65rem .85rem;
            margin-bottom: 1.25rem;
        }
        .skip-note i { color: var(--brand); font-size: 1.1rem; }

        .btn-skip {
            position: relative;
            overflow: hidden;
            border-radius: 999px;
            color: #dc3545;
            background: transparent;
            border: 1px solid transparent;
            font-weight: 600;
        }
        .btn-skip:hover:not(:disabled) { background: #fff1f1; border-color: #f5c2c7; color: #b02a37; }
        .btn-skip:disabled { color: var(--muted); opacity: 1; }
        .btn-skip .skip-progress {
            position: absolute; inset: 0;
            background: rgba(var(--brand-rgb), .08);
            transform-origin: left;
            transform: scaleX(0);
        }
        .btn-skip .skip-label { position: relative; }
        .btn-skip.counting .skip-progress { animation: fillBar 3s linear forwards; }

        /* Animasi isi modal, diputar ulang setiap modal dibuka */
        #skipModal.show .warn-icon { animation: iconPop .7s cubic-bezier(.34, 1.56, .64, 1) .1s backwards; }
        #skipModal.show .warn-icon > i { display: inline-block; animation: lockWiggle .7s ease-in-out .7s; }
        #skipModal.show .warn-icon::after { animation: warnRing 2s ease-out 1.2s infinite; }
        #skipModal.show .m-reveal { animation: fadeUp .5s cubic-bezier(.2, .8, .2, 1) backwards; animation-delay: calc(.2s + var(--i, 0) * .08s); }
        #skipModal.show .risk-item { animation: slideIn .5s cubic-bezier(.2, .8, .2, 1) backwards; animation-delay: calc(.35s + var(--i, 0) * .1s); }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-18px); }
            to { opacity: 1; transform: none; }
        }
        @keyframes warnRing {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, .5); }
            70%, 100% { box-shadow: 0 0 0 16px rgba(245, 158, 11, 0); }
        }
        @keyframes fillBar {
            to { transform: scaleX(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    <span class="bubble b1"></span>
    <span class="bubble b2"></span>

    <div class="pass-card">
        <div class="pass-head">
            <div class="pass-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h1 class="reveal" style="--i: 0">Ganti Password</h1>
            <p class="reveal" style="--i: 1">
                <?php if ($forced): ?>
                    Akun Anda masih memakai password sementara. Ganti dulu sebelum melanjutkan.
                <?php else: ?>
                    Masuk sebagai <?= e(auth('email')) ?>
                <?php endif; ?>
            </p>
        </div>

        <?php foreach (['error' => 'exclamation-triangle-fill', 'warning' => 'shield-exclamation', 'success' => 'check-circle-fill'] as $type => $icon): ?>
            <?php if (hasFlash($type)): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> d-flex align-items-center gap-2 reveal" style="--i: 2" role="alert">
                    <i class="bi bi-<?= $icon ?>"></i>
                    <div><?= getFlash($type) ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <form action="<?= url('auth/simpan-password') ?>" method="POST" id="passwordForm">
            <?= csrfField() ?>
            <input type="text" name="username" value="<?= e(auth('email')) ?>" autocomplete="username" hidden>

            <div class="mb-3 reveal" style="--i: 3">
                <label for="current_password" class="form-label small fw-semibold">Password saat ini</label>
                <div class="input-pill">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" class="form-control" id="current_password" name="current_password"
                           autocomplete="current-password" required autofocus>
                </div>
            </div>

            <div class="mb-3 reveal" style="--i: 4">
                <label for="new_password" class="form-label small fw-semibold">Password baru</label>
                <div class="input-pill">
                    <i class="bi bi-key-fill"></i>
                    <input type="password" class="form-control" id="new_password" name="new_password"
                           autocomplete="new-password" minlength="8" required>
                </div>
                <div class="form-text">Minimal 8 karakter.</div>
            </div>

            <div class="mb-4 reveal" style="--i: 5">
                <label for="confirm_password" class="form-label small fw-semibold">Ulangi password baru</label>
                <div class="input-pill">
                    <i class="bi bi-key-fill"></i>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                           autocomplete="new-password" minlength="8" required>
                </div>
                <div class="match-hint" id="matchHint" aria-live="polite"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-pill w-100 mb-3 reveal" style="--i: 6" id="submitBtn">
                Simpan Password
            </button>
        </form>

        <?php $canSkip = $forced && empty(auth('password_change_skipped')); ?>

        <?php if ($canSkip): ?>
            <button type="button" class="btn btn-outline-secondary btn-lg w-100 mb-3 reveal" style="--i: 7; border-radius: 999px;"
                    data-bs-toggle="modal" data-bs-target="#skipModal">
                Nanti saja
            </button>
        <?php endif; ?>

        <div class="text-center reveal" style="--i: 8">
            <?php if ($forced): ?>
                <form action="<?= url('auth/logout') ?>" method="POST" class="d-inline">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-link small fw-bold text-danger text-decoration-none">
                        <i class="bi bi-box-arrow-left me-1"></i>Logout
                    </button>
                </form>
            <?php endif; ?>
            <?php if (!$canSkip): ?>
                <a href="<?= url('admin/dashboard') ?>" class="small fw-bold">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($canSkip): ?>
        <div class="modal fade" id="skipModal" tabindex="-1" aria-labelledby="skipModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content skip-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

                    <div class="warn-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <h2 class="m-reveal" style="--i: 0" id="skipModalLabel">Tunda ganti password?</h2>
                    <p class="skip-lead m-reveal" style="--i: 1">Password Anda saat ini adalah password sementara yang mungkin sudah diketahui orang lain. Jika ditunda:</p>

                    <ul class="risk-list">
                        <li class="risk-item" style="--i: 0">
                            <div class="risk-icon"><i class="bi bi-person-fill-exclamation"></i></div>
                            <div><strong>Akun bisa dipakai orang lain</strong><span>Siapa pun yang tahu password ini bisa masuk ke panel admin atas nama Anda.</span></div>
                        </li>
                        <li class="risk-item" style="--i: 1">
                            <div class="risk-icon"><i class="bi bi-people-fill"></i></div>
                            <div><strong>Data jemaat bisa bocor</strong><span>Nama, alamat, nomor telepon, dan data keluarga bisa dilihat, diubah, atau dicuri.</span></div>
                        </li>
                        <li class="risk-item" style="--i: 2">
                            <div class="risk-icon"><i class="bi bi-pencil-square"></i></div>
                            <div><strong>Konten website bisa diubah</strong><span>Artikel, kegiatan, dan rekening menabur bisa diganti tanpa sepengetahuan Anda.</span></div>
                        </li>
                    </ul>

                    <div class="skip-note m-reveal" style="--i: 5">
                        <i class="bi bi-clock-history"></i>
                        <div>Penundaan hanya berlaku untuk sesi login ini. Anda akan diminta lagi pada login berikutnya.</div>
                    </div>

                    <div class="d-grid gap-2 m-reveal" style="--i: 6">
                        <button type="button" class="btn btn-primary btn-lg btn-pill" data-bs-dismiss="modal">
                            Ganti sekarang
                        </button>
                        <form action="<?= url('auth/lewati-ganti-password') ?>" method="POST" class="d-grid">
                            <?= csrfField() ?>
                            <button type="submit" class="btn btn-skip" id="skipBtn" disabled>
                                <span class="skip-progress"></span>
                                <span class="skip-label">Saya mengerti, tunda</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php endif; ?>

    <script>
        // Indikator apakah konfirmasi password sudah cocok
        const newPass = document.getElementById('new_password');
        const confirmPass = document.getElementById('confirm_password');
        const hint = document.getElementById('matchHint');

        function updateHint() {
            if (!confirmPass.value) {
                hint.className = 'match-hint';
                return;
            }
            const ok = confirmPass.value === newPass.value;
            hint.className = 'match-hint show ' + (ok ? 'ok' : 'bad');
            hint.innerHTML = ok
                ? '<i class="bi bi-check-circle-fill me-1"></i>Password cocok'
                : '<i class="bi bi-x-circle-fill me-1"></i>Password belum cocok';
        }
        newPass.addEventListener('input', updateHint);
        confirmPass.addEventListener('input', updateHint);

        // Tombol "tunda" baru aktif setelah 3 detik, agar peringatan sempat dibaca
        const skipModal = document.getElementById('skipModal');
        if (skipModal) {
            const skipBtn = document.getElementById('skipBtn');
            const skipLabel = skipBtn.querySelector('.skip-label');
            let timer;

            skipModal.addEventListener('shown.bs.modal', function () {
                let left = 3;
                skipBtn.disabled = true;
                skipBtn.classList.remove('counting');
                void skipBtn.offsetWidth; // restart animasi progress
                skipBtn.classList.add('counting');
                skipLabel.textContent = 'Saya mengerti, tunda (' + left + ')';

                clearInterval(timer);
                timer = setInterval(function () {
                    left--;
                    if (left > 0) {
                        skipLabel.textContent = 'Saya mengerti, tunda (' + left + ')';
                        return;
                    }
                    clearInterval(timer);
                    skipBtn.disabled = false;
                    skipLabel.textContent = 'Saya mengerti, tunda';
                }, 1000);
            });

            skipModal.addEventListener('hidden.bs.modal', function () {
                clearInterval(timer);
                skipBtn.disabled = true;
                skipBtn.classList.remove('counting');
            });
        }

        // Tampilkan spinner & cegah submit ganda
        document.getElementById('passwordForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Menyimpan...';
        });
    </script>
</body>
</html>
