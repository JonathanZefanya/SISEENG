-- =====================================================
-- Migration: Tambah flag wajib ganti password
-- User dengan flag ini dipaksa mengganti password
-- sebelum bisa membuka halaman admin.
-- =====================================================

ALTER TABLE `users`
    ADD COLUMN `must_change_password` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password`;

-- Akun yang masih memakai password default (admin123) otomatis
-- ditandai saat login, jadi tidak perlu di-set manual di sini.
