<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">Edit Admin</h1>
        <p class="text-muted mb-0">Perbarui informasi akun admin</p>
    </div>
    <a href="<?= url('admin/users') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Form -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= url('admin/users/update') ?>" method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="name" name="name" 
                           value="<?= e(old('name') ?: $user['name']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                           value="<?= e(old('email') ?: $user['email']) ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-lg" id="password" name="password" 
                               minlength="8" placeholder="Kosongkan jika tidak ingin mengubah">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>
                
                <div class="col-md-6">
                    <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="role" name="role" required>
                        <option value="<?= ROLE_ADMIN ?>" <?= $user['role'] === ROLE_ADMIN ? 'selected' : '' ?>>Admin</option>
                        <option value="<?= ROLE_SUPER_ADMIN ?>" <?= $user['role'] === ROLE_SUPER_ADMIN ? 'selected' : '' ?>>Super Admin</option>
                    </select>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= url('admin/users') ?>" class="btn btn-outline-secondary btn-lg">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
