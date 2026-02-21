<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Edit Data Jemaat</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('admin/members') ?>">Data Jemaat</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
    <a href="<?= url('admin/members') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= url('admin/members/update/' . $member['id']) ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= $member['id'] ?>">

            <h5 class="fw-bold mb-4 pb-2 border-bottom">
                <i class="bi bi-person text-primary me-2"></i>Data Pribadi
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="full_name" class="form-label fw-semibold">Nama Lengkap <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name"
                        value="<?= e($member['full_name']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="gender" class="form-label fw-semibold">Jenis Kelamin <span
                            class="text-danger">*</span></label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="">Pilih...</option>
                        <option value="M" <?= $member['gender'] === 'M' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="F" <?= $member['gender'] === 'F' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="birth_date" class="form-label fw-semibold">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date"
                        value="<?= e($member['birth_date']) ?>">
                </div>

                <div class="col-md-6">
                    <label for="birth_place" class="form-label fw-semibold">Tempat Lahir</label>
                    <input type="text" class="form-control" id="birth_place" name="birth_place"
                        value="<?= e($member['birth_place']) ?>">
                </div>
            </div>

            <h5 class="fw-bold mb-4 pb-2 border-bottom mt-5">
                <i class="bi bi-telephone text-primary me-2"></i>Kontak
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">No. Telepon</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= e($member['phone']) ?>"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= e($member['email']) ?>">
                </div>

                <div class="col-12">
                    <label for="address" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="address" name="address"
                        rows="3"><?= e($member['address']) ?></textarea>
                </div>
            </div>

            <h5 class="fw-bold mb-4 pb-2 border-bottom mt-5">
                <i class="bi bi-info-circle text-primary me-2"></i>Informasi Lainnya
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="baptism_date" class="form-label fw-semibold">Tanggal Baptis</label>
                    <input type="date" class="form-control" id="baptism_date" name="baptism_date"
                        value="<?= e($member['baptism_date']) ?>">
                </div>

                <div class="col-md-6">
                    <label for="membership_date" class="form-label fw-semibold">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="membership_date" name="membership_date"
                        value="<?= e($member['membership_date']) ?>">
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Tidak Aktif
                        </option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label fw-semibold">Catatan</label>
                    <textarea class="form-control" id="notes" name="notes"
                        rows="3"><?= e($member['notes']) ?></textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <div class="small text-muted">
                    <p class="mb-1">
                        <i class="bi bi-calendar me-1"></i>
                        Dibuat: <?= formatDate($member['created_at']) ?>
                    </p>
                    <?php if ($member['updated_at']): ?>
                        <p class="mb-0">
                            <i class="bi bi-pencil me-1"></i>
                            Diupdate: <?= formatDate($member['updated_at']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= url('admin/members') ?>" class="btn btn-outline-secondary btn-lg">Batal</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>