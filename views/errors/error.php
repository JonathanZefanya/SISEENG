<?php
/**
 * Template halaman error (403, 404, 500, 503, ...)
 * Dipanggil lewat showError() di core/Helpers.php
 *
 * @var int $code
 * @var string $heading
 * @var string $message
 * @var string|null $detail Detail teknis, hanya diisi di mode development
 */
$retry = $code >= 500;
?>
<!DOCTYPE html>
<html lang="id" class="intro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="<?= themeColor() ?>">
    <meta name="robots" content="noindex">
    <title><?= $code ?> - <?= e($heading) ?></title>

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

        .error-card {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(var(--brand-rgb), .25);
        }

        /* ===== Panel brand dengan tepi awan ===== */
        .error-panel {
            position: relative;
            overflow: hidden;
            color: #fff;
            text-align: center;
            padding: 2.75rem 1.5rem 5rem;
            background:
                radial-gradient(circle at 85% 15%, rgba(255, 255, 255, .18), transparent 45%),
                linear-gradient(135deg, var(--brand-light) 0%, var(--brand) 45%, var(--brand-darker) 100%);
        }

        .panel-content { position: relative; z-index: 2; }

        .error-logo {
            width: 64px; height: 64px;
            border-radius: 20px;
            background: #fff;
            color: var(--brand);
            display: inline-grid; place-items: center;
            font-size: 1.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
            margin-bottom: 1rem;
        }
        .error-logo .brand-logo-img { width: 100%; height: 100%; object-fit: contain; padding: 9px; }

        .error-code {
            font-size: clamp(4.5rem, 18vw, 6.5rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.04em;
            text-shadow: 0 10px 30px rgba(0, 0, 0, .15);
        }

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

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            z-index: 0;
        }
        .bubble.b1 { width: 240px; height: 240px; right: -90px; top: -90px; }
        .bubble.b2 { width: 110px; height: 110px; left: -30px; top: 35%; }

        /* ===== Isi ===== */
        .error-body {
            text-align: center;
            padding: .5rem 1.5rem 2.25rem;
            position: relative;
            z-index: 2;
        }
        .error-body h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: .5rem; }
        .error-body p { color: var(--muted); line-height: 1.6; margin-bottom: 1.75rem; }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            justify-content: center;
        }
        .error-actions .btn {
            border-radius: 999px;
            padding: .7rem 1.5rem;
            font-weight: 700;
        }
        .error-actions .btn-primary { box-shadow: 0 10px 24px rgba(var(--brand-rgb), .35); }
        .error-actions .btn-light {
            background: var(--brand-soft);
            border-color: transparent;
            color: var(--brand-dark);
        }
        .error-actions .btn-light:hover { background: rgba(var(--brand-rgb), .18); color: var(--brand-darker); }

        /* Detail teknis (mode development saja) */
        .error-detail {
            text-align: left;
            margin: 1.75rem 0 0;
            padding: 1rem;
            max-height: 240px;
            overflow: auto;
            font-size: .78rem;
            white-space: pre-wrap;
            word-break: break-word;
            color: var(--ink-2);
            background: var(--canvas);
            border-radius: var(--radius-sm);
        }

        @media (max-width: 400px) {
            .error-actions .btn { width: 100%; }
        }

        /* ===== Animasi masuk ===== */
        .bubble { animation: bubbleFloat 9s ease-in-out infinite alternate; }
        .bubble.b2 { animation-duration: 7s; animation-delay: -3s; }

        .intro .error-card { animation: cardIn .7s cubic-bezier(.2, .8, .2, 1) both; }
        .intro .error-panel { animation: panelReveal .9s cubic-bezier(.7, 0, .2, 1) .15s both; }
        .intro .cloud { animation: cloudPop .6s cubic-bezier(.34, 1.56, .64, 1) both; }
        .intro .cloud:nth-child(1) { animation-delay: .55s; }
        .intro .cloud:nth-child(2) { animation-delay: .62s; }
        .intro .cloud:nth-child(3) { animation-delay: .69s; }
        .intro .cloud:nth-child(4) { animation-delay: .76s; }
        .intro .cloud:nth-child(5) { animation-delay: .83s; }

        .intro .error-logo { animation: logoIn .9s cubic-bezier(.34, 1.56, .64, 1) .7s both; }
        .intro .error-code { animation: fadeUp .6s ease .9s both; }
        .intro .error-body > * { animation: fadeUp .55s cubic-bezier(.2, .8, .2, 1) both; }
        .intro .error-body > :nth-child(1) { animation-delay: .5s; }
        .intro .error-body > :nth-child(2) { animation-delay: .6s; }
        .intro .error-body > :nth-child(3) { animation-delay: .7s; }
        .intro .error-body > :nth-child(4) { animation-delay: .8s; }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(.96); }
            to { opacity: 1; transform: none; }
        }
        @keyframes panelReveal {
            from { clip-path: inset(0 0 100% 0); }
            to { clip-path: inset(0 0 0 0); }
        }
        @keyframes cloudPop {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        @keyframes logoIn {
            0% { opacity: 0; transform: translateY(-30px) scale(.4) rotate(-20deg); }
            60% { opacity: 1; transform: translateY(4px) scale(1.08) rotate(4deg); }
            100% { opacity: 1; transform: none; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: none; }
        }
        @keyframes bubbleFloat {
            from { transform: translate(0, 0); }
            to { transform: translate(-18px, 22px); }
        }

        @media (prefers-reduced-motion: reduce) {
            .bubble, .intro *, .intro .error-card { animation: none !important; }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-panel">
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="cloud"></span>
            <span class="bubble b1"></span>
            <span class="bubble b2"></span>

            <div class="panel-content">
                <div class="error-logo"><?= brandMark() ?></div>
                <div class="error-code"><?= $code ?></div>
            </div>
        </div>

        <div class="error-body">
            <h1><?= e($heading) ?></h1>
            <p><?= e($message) ?></p>
            <div class="error-actions">
                <a href="<?= url() ?>" class="btn btn-primary">
                    <i class="bi bi-house-door-fill me-2"></i>Kembali ke Beranda
                </a>
                <?php if ($retry): ?>
                    <button type="button" onclick="location.reload()" class="btn btn-light">
                        <i class="bi bi-arrow-clockwise me-2"></i>Muat Ulang
                    </button>
                <?php else: ?>
                    <button type="button" onclick="history.back()" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </button>
                <?php endif; ?>
            </div>
            <?php if (!empty($detail)): ?>
                <pre class="error-detail"><?= e($detail) ?></pre>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
