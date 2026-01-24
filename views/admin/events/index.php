<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Kelola Kegiatan</h1>
        <p class="text-muted mb-0">Buat dan kelola kegiatan/acara gereja</p>
    </div>
    <a href="<?= url('admin/events/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Kegiatan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted display-1"></i>
                <h4 class="mt-3 text-muted">Belum ada kegiatan</h4>
                <p class="text-muted">Klik tombol "Tambah Kegiatan" untuk membuat kegiatan baru</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Kegiatan</th>
                            <th>Tanggal & Waktu</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <?php 
                            $eventDate = strtotime($event['event_date']);
                            $today = strtotime(date('Y-m-d'));
                            $isPast = $eventDate < $today;
                            $isToday = $eventDate === $today;
                            ?>
                            <tr class="<?= $isPast ? 'table-light' : '' ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if ($event['image']): ?>
                                            <img src="<?= asset('uploads/events/' . e($event['image'])) ?>" 
                                                 alt="<?= e($event['title']) ?>"
                                                 class="rounded me-3"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-calendar-event fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= e($event['title']) ?></h6>
                                            <small class="text-muted">
                                                <?= e(substr($event['description'], 0, 50)) ?>...
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="bi bi-calendar text-primary me-1"></i>
                                        <?= formatDate($event['event_date']) ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= date('H:i', strtotime($event['event_time'])) ?> WIB
                                    </small>
                                </td>
                                <td>
                                    <?php if ($event['location']): ?>
                                        <small>
                                            <i class="bi bi-geo-alt text-primary me-1"></i>
                                            <?= e($event['location']) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isToday): ?>
                                        <span class="badge bg-primary">Hari Ini</span>
                                    <?php elseif ($isPast): ?>
                                        <span class="badge bg-secondary">Sudah Lewat</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Akan Datang</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= url('kegiatan/' . $event['id']) ?>" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= url('admin/events/edit/' . $event['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Hapus"
                                                onclick="confirmDelete(<?= $event['id'] ?>, '<?= e($event['title']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pagination) && isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
                <div class="card-footer bg-white border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/events?page=' . ($pagination['current_page'] - 1)) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('admin/events?page=' . $i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= url('admin/events?page=' . ($pagination['current_page'] + 1)) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-warning display-3"></i>
                <h4 class="mt-3">Apakah Anda yakin?</h4>
                <p class="text-muted mb-0">Kegiatan "<span id="eventTitle"></span>" akan dihapus permanen.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('eventTitle').textContent = title;
    document.getElementById('deleteForm').action = '<?= url('admin/events/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
