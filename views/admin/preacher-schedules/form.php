<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold">
            <i class="bi bi-calendar-week text-primary me-2"></i>
            <?= $isEdit ? 'Edit' : 'Tambah' ?> Jadwal Pengkhotbah
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/jadwal-pengkhotbah') ?>">Jadwal Pengkhotbah</a></li>
                <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Tambah' ?></li>
            </ol>
        </nav>
    </div>
    <a href="<?= url('admin/jadwal-pengkhotbah') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-journal-text me-2 text-primary"></i>
                    Form Jadwal
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= $isEdit ? url('admin/jadwal-pengkhotbah/update/' . $schedule['id']) : url('admin/jadwal-pengkhotbah/store') ?>" method="POST">
                    <?= csrfField() ?>
                    
                    <div class="row g-4">
                        <!-- Tanggal -->
                        <div class="col-md-6">
                            <label for="schedule_date" class="form-label fw-semibold">
                                Tanggal Ibadah <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control form-control-lg" id="schedule_date" 
                                   name="schedule_date" required
                                   value="<?= e($schedule['schedule_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        
                        <!-- Jenis Ibadah (dari jadwal ibadah) -->
                        <div class="col-md-6">
                            <label for="ibadah_select" class="form-label fw-semibold">
                                Jenis Ibadah <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="ibadah_select" required onchange="updateServiceFields()">
                                <option value="">-- Pilih Jenis Ibadah --</option>
                                <?php foreach ($ibadahList as $ibadah): ?>
                                <option value="<?= e($ibadah['title']) ?>|<?= e($ibadah['start_time']) ?>"
                                    <?= ($schedule && $schedule['service_name'] === $ibadah['title'] && $schedule['service_time'] === $ibadah['start_time']) ? 'selected' : '' ?>>
                                    <?= e($ibadah['title']) ?> (<?= e($ibadah['day_of_week']) ?> - <?= date('H:i', strtotime($ibadah['start_time'])) ?> WIB)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="service_name" id="service_name" value="<?= e($schedule['service_name'] ?? '') ?>">
                            <input type="hidden" name="service_time" id="service_time" value="<?= e($schedule['service_time'] ?? '') ?>">
                        </div>
                        
                        <!-- Pengkhotbah -->
                        <div class="col-md-6">
                            <label for="preacher_name" class="form-label fw-semibold">
                                Nama Pengkhotbah
                            </label>
                            <input type="text" class="form-control form-control-lg" id="preacher_name" 
                                   name="preacher_name" list="preacherList"
                                   placeholder="Kosongkan jika belum diketahui"
                                   value="<?= e($schedule['preacher_name'] ?? '') ?>">
                            <datalist id="preacherList">
                                <?php foreach ($preachers as $preacher): ?>
                                <option value="<?= e($preacher) ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <small class="text-muted">Kosongkan jika pengkhotbah belum ditentukan</small>
                        </div>
                        
                        <!-- Info Waktu -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu Ibadah</label>
                            <div class="form-control form-control-lg bg-light" id="waktu_display">
                                <?php if ($schedule && $schedule['service_time']): ?>
                                <?= date('H:i', strtotime($schedule['service_time'])) ?> WIB
                                <?php else: ?>
                                <span class="text-muted">Pilih jenis ibadah terlebih dahulu</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Judul Khotbah -->
                        <div class="col-12">
                            <label for="sermon_title" class="form-label fw-semibold">
                                Judul Khotbah
                            </label>
                            <input type="text" class="form-control form-control-lg" id="sermon_title" 
                                   name="sermon_title"
                                   placeholder="Judul atau tema khotbah (opsional)"
                                   value="<?= e($schedule['sermon_title'] ?? '') ?>">
                        </div>
                        
                        <!-- Catatan -->
                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold">
                                Catatan
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Catatan tambahan (opsional)"><?= e($schedule['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-check-lg me-2"></i>
                            <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Jadwal' ?>
                        </button>
                        <a href="<?= url('admin/jadwal-pengkhotbah') ?>" class="btn btn-outline-secondary btn-lg">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Pengkhotbah</strong>: Bisa dikosongkan jika belum ditentukan. Akan tampil sebagai "Belum Ditentukan" di website.
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Jenis Ibadah</strong>: Pilih dari daftar jadwal ibadah yang sudah terdaftar.
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Judul Khotbah</strong>: Opsional, bisa diisi setelah tema khotbah diketahui.
                    </li>
                    <li>
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Tidak boleh ada jadwal duplikat (tanggal + waktu yang sama).
                    </li>
                </ul>
            </div>
        </div>
        
        <?php if (!empty($preachers)): ?>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>Pengkhotbah Terdaftar</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($preachers as $preacher): ?>
                    <span class="badge bg-light text-dark border"><?= e($preacher) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateServiceFields() {
    const select = document.getElementById('ibadah_select');
    const serviceNameInput = document.getElementById('service_name');
    const serviceTimeInput = document.getElementById('service_time');
    const waktuDisplay = document.getElementById('waktu_display');
    
    if (select.value) {
        const [serviceName, serviceTime] = select.value.split('|');
        serviceNameInput.value = serviceName;
        serviceTimeInput.value = serviceTime;
        
        // Format waktu untuk display (HH:MM)
        const timeParts = serviceTime.split(':');
        waktuDisplay.innerHTML = timeParts[0] + ':' + timeParts[1] + ' WIB';
    } else {
        serviceNameInput.value = '';
        serviceTimeInput.value = '';
        waktuDisplay.innerHTML = '<span class="text-muted">Pilih jenis ibadah terlebih dahulu</span>';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Jika edit mode, pastikan dropdown terisi dengan benar
    const select = document.getElementById('ibadah_select');
    if (!select.value && document.getElementById('service_name').value) {
        // Cari option yang sesuai
        const serviceName = document.getElementById('service_name').value;
        const serviceTime = document.getElementById('service_time').value;
        const targetValue = serviceName + '|' + serviceTime;
        
        for (let option of select.options) {
            if (option.value === targetValue) {
                option.selected = true;
                break;
            }
        }
    }
});
</script>
