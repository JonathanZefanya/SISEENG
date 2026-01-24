/**
 * SISEENG - Admin Panel JavaScript
 */

(function() {
    'use strict';

    // =====================================================
    // DOM Ready
    // =====================================================
    document.addEventListener('DOMContentLoaded', function() {
        initSidebar();
        initDropdowns();
        initDataTables();
        initDeleteConfirmation();
        initFormEnhancements();
        initTooltips();
        initAlertAutoDismiss();
        initImagePreview();
    });

    // =====================================================
    // Sidebar Toggle
    // =====================================================
    function initSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const content = document.querySelector('.admin-content');

        if (!sidebar || !sidebarToggle) return;

        // Toggle sidebar
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
        });

        // Close sidebar on overlay click
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Close sidebar on window resize (desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
            }
        });

        // Active menu highlight
        highlightActiveMenu();
    }

    function highlightActiveMenu() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href) && href !== '/admin') {
                link.classList.add('active');
            }
        });
    }

    // =====================================================
    // Dropdown Menus
    // =====================================================
    function initDropdowns() {
        const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                // Let Bootstrap handle the dropdown
            });
        });
    }

    // =====================================================
    // Data Tables Enhancement
    // =====================================================
    function initDataTables() {
        // Search functionality
        const searchInputs = document.querySelectorAll('[data-table-search]');
        
        searchInputs.forEach(input => {
            const tableId = input.dataset.tableSearch;
            const table = document.getElementById(tableId);
            
            if (!table) return;

            input.addEventListener('input', debounce(function() {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }, 300));
        });

        // Sortable columns
        const sortableHeaders = document.querySelectorAll('th[data-sort]');
        
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTable(this);
            });
        });
    }

    function sortTable(header) {
        const table = header.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const sortType = header.dataset.sort;
        const isAscending = header.classList.contains('sort-asc');

        rows.sort((a, b) => {
            let aValue = a.children[columnIndex].textContent.trim();
            let bValue = b.children[columnIndex].textContent.trim();

            if (sortType === 'number') {
                aValue = parseFloat(aValue) || 0;
                bValue = parseFloat(bValue) || 0;
            } else if (sortType === 'date') {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            }

            if (isAscending) {
                return aValue > bValue ? -1 : 1;
            } else {
                return aValue < bValue ? -1 : 1;
            }
        });

        // Update sort indicators
        header.parentNode.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');

        // Re-append rows
        rows.forEach(row => tbody.appendChild(row));
    }

    // =====================================================
    // Delete Confirmation
    // =====================================================
    function initDeleteConfirmation() {
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('[data-confirm-delete]');
            if (!deleteBtn) return;

            e.preventDefault();
            
            const message = deleteBtn.dataset.confirmDelete || 'Apakah Anda yakin ingin menghapus item ini?';
            const formId = deleteBtn.dataset.formId;
            
            if (formId) {
                showConfirmModal(message, () => {
                    document.getElementById(formId).submit();
                });
            } else if (deleteBtn.form) {
                showConfirmModal(message, () => {
                    deleteBtn.form.submit();
                });
            }
        });
    }

    function showConfirmModal(message, onConfirm) {
        // Check if modal already exists
        let modal = document.getElementById('confirmDeleteModal');
        
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'confirmDeleteModal';
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center py-4">
                            <i class="bi bi-exclamation-triangle text-warning display-3"></i>
                            <h4 class="mt-3">Konfirmasi</h4>
                            <p class="text-muted mb-0" id="confirmMessage"></p>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pt-0">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">Hapus</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        document.getElementById('confirmMessage').textContent = message;
        
        const bsModal = new bootstrap.Modal(modal);
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        // Remove old event listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.addEventListener('click', function() {
            bsModal.hide();
            onConfirm();
        });
        
        bsModal.show();
    }

    // =====================================================
    // Form Enhancements
    // =====================================================
    function initFormEnhancements() {
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        
        if (titleInput && slugInput) {
            titleInput.addEventListener('input', function() {
                if (!slugInput.dataset.modified) {
                    slugInput.value = generateSlug(this.value);
                }
            });
            
            slugInput.addEventListener('input', function() {
                this.dataset.modified = 'true';
            });
        }

        // Character counter for textareas
        document.querySelectorAll('textarea[data-max-length]').forEach(textarea => {
            const maxLength = parseInt(textarea.dataset.maxLength);
            const counter = document.createElement('small');
            counter.className = 'text-muted d-block text-end mt-1';
            
            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${remaining} karakter tersisa`;
                counter.classList.toggle('text-danger', remaining < 20);
            }
            
            textarea.parentNode.appendChild(counter);
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });

        // Prevent double form submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                    
                    // Re-enable after timeout (fallback)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 10000);
                }
            });
        });
    }

    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // =====================================================
    // Image Preview
    // =====================================================
    function initImagePreview() {
        document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
            const previewId = input.dataset.preview;
            const previewEl = document.getElementById(previewId);
            
            if (!previewEl) return;

            input.addEventListener('change', function() {
                previewEl.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail mt-2';
                        img.style.maxHeight = '200px';
                        previewEl.appendChild(img);
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    }

    // =====================================================
    // Tooltips
    // =====================================================
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // =====================================================
    // Auto-dismiss Alerts
    // =====================================================
    function initAlertAutoDismiss() {
        document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
            const delay = parseInt(alert.dataset.autoDismiss) || 5000;
            
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, delay);
        });
    }

    // =====================================================
    // Utility Functions
    // =====================================================
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Show toast notification
     */
    window.showAdminToast = function(message, type = 'info') {
        const toastContainer = getOrCreateToastContainer();
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${getIconForType(type)} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toastEl);
        
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 4000
        });
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    };

    function getOrCreateToastContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }

    function getIconForType(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'exclamation-circle-fill',
            danger: 'exclamation-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || 'info-circle-fill';
    }

    /**
     * Format number with thousand separator
     */
    window.formatNumber = function(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    };

    /**
     * Export table to CSV
     */
    window.exportTableToCSV = function(tableId, filename = 'export.csv') {
        const table = document.getElementById(tableId);
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            
            cols.forEach(col => {
                // Skip action columns
                if (!col.classList.contains('no-export')) {
                    let text = col.textContent.trim().replace(/"/g, '""');
                    rowData.push(`"${text}"`);
                }
            });
            
            csv.push(rowData.join(','));
        });

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    };

    // =====================================================
    // AJAX Helper
    // =====================================================
    window.adminAjax = {
        post: function(url, data, options = {}) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    ...options.headers
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && options.onSuccess) {
                    options.onSuccess(data);
                } else if (!data.success && options.onError) {
                    options.onError(data);
                }
                return data;
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                if (options.onError) {
                    options.onError({ message: 'Terjadi kesalahan jaringan' });
                }
            });
        },

        get: function(url, options = {}) {
            return fetch(url, {
                method: 'GET',
                headers: options.headers || {}
            })
            .then(response => response.json())
            .then(data => {
                if (options.onSuccess) {
                    options.onSuccess(data);
                }
                return data;
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                if (options.onError) {
                    options.onError({ message: 'Terjadi kesalahan jaringan' });
                }
            });
        }
    };

})();
