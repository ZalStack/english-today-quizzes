-- =============================================
-- 1. Buat Divisions (jalankan via phpMyAdmin)
-- =============================================
INSERT INTO `divisions` (`name`, `description`, `created_at`, `updated_at`) VALUES
('HRD', 'Human Resources Department', NOW(), NOW()),
('STAFSUS', 'Staff Khusus', NOW(), NOW()),
('KPJ', 'Klinik Pendidikan MIPA', NOW(), NOW()),
('PENDIDIKAN BOGOR', 'Pendidikan Bogor', NOW(), NOW()),
('LPS', 'Lembaga Pendidikan', NOW(), NOW()),
('PENDIDIKAN JAKARTA', 'Pendidikan Jakarta', NOW(), NOW()),
('MEDIA', 'Media', NOW(), NOW()),
('RG', 'Research Group', NOW(), NOW()),
('KEUANGAN', 'Keuangan', NOW(), NOW()),
('PKA', 'PKA', NOW(), NOW()),
('SAPRAS OB', 'Sarana Prasarana Office Boy', NOW(), NOW()),
('SAPRAS DRIVER', 'Sarana Prasarana Driver', NOW(), NOW());

-- =============================================
-- 2. Update division_id for each employee
-- Ganti 'user@email.com' dengan email asli masing2 pegawai
-- =============================================
-- HRD
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'HRD') WHERE email = 'budi@email.com';

-- STAFSUS
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'STAFSUS') WHERE email = 'thyeadi@email.com';

-- KPJ
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'KPJ') WHERE email IN (
  'moh.napis@email.com', 'irvan.sanjaya@email.com', 'ryky.tunggal@email.com',
  'peggy.nurida@email.com', 'muhamad.ihsan@email.com'
);

-- PENDIDIKAN BOGOR
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'PENDIDIKAN BOGOR') WHERE email IN (
  'fikri.fauzi@email.com', 'mia.nur@email.com', 'zidan.mahardika@email.com',
  'febriyana@email.com', 'devi.novi@email.com'
);

-- LPS
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'LPS') WHERE email IN (
  'ardianto@email.com', 'buchori.nugraha@email.com', 'fazar.zulham@email.com',
  'agus.sutisna@email.com', 'nanda.lindawati@email.com', 'tri.jumsari@email.com'
);

-- PENDIDIKAN JAKARTA
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'PENDIDIKAN JAKARTA') WHERE email IN (
  'rijwan@email.com', 'khomsalia@email.com', 'desi.dwi@email.com',
  'rangga.pradana@email.com', 'cahya.aditya@email.com', 'ramadhan.setiawan@email.com',
  'siti.khoerunnisa@email.com'
);

-- MEDIA
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'MEDIA') WHERE email IN (
  'siti.fatimah@email.com', 'roy.yulio@email.com', 'sang.baharsyah@email.com',
  'ali.zulfikar@email.com'
);

-- RG
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'RG') WHERE email IN (
  'muhamad.seis@email.com', 'hendra.minar@email.com', 'weni.wulan@email.com',
  'nabila.nurhakimah@email.com'
);

-- KEUANGAN
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'KEUANGAN') WHERE email IN (
  'desi.kurnia@email.com', 'dwi.atika@email.com'
);

-- PKA
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'PKA') WHERE email IN (
  'ferdianto@email.com', 'siti.maesaroh@email.com', 'muchammad.fachri@email.com',
  'dedi.wahyudi@email.com', 'sutriyati@email.com', 'vega.oktaviana@email.com',
  'andri.imam@email.com', 'siti.alpiyah@email.com'
);

-- SAPRAS OB
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'SAPRAS OB') WHERE email IN (
  'lukmanul.hakim@email.com', 'rully.dwiandika@email.com', 'muhamad.rijal@email.com'
);

-- SAPRAS DRIVER
UPDATE users SET division_id = (SELECT id FROM divisions WHERE name = 'SAPRAS DRIVER') WHERE email IN (
  'ragil.agustian@email.com', 'deri.rahman@email.com'
);
