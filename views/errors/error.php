<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <div class="display-1 text-primary fw-bold">404</div>
                <h1 class="h3 mb-3">Halaman Tidak Ditemukan</h1>
                <p class="text-muted mb-4">
                    Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= url() ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-house me-2"></i>Kembali ke Beranda
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
