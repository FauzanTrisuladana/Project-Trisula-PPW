DELIMITER $$

CREATE PROCEDURE sp_register_koperasi (
    IN  p_nama_koperasi   VARCHAR(255),
    IN  p_alamat          TEXT,
    IN  p_nama_kota       VARCHAR(100),
    IN  p_simpanan_pokok  INT,
    IN  p_simpanan_wajib  INT,
    IN  p_username        VARCHAR(50),
    IN  p_password_hash   VARCHAR(255),
    IN  p_email           VARCHAR(100)
)
BEGIN
    DECLARE v_kota_id     INT;
    DECLARE v_koperasi_id INT;
    DECLARE v_akun_id     INT;
    DECLARE v_login_id    INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pendaftaran gagal – transaksi dibatalkan.';
    END;

    START TRANSACTION;

        -- Ambil id_kota berdasarkan nama kota
        SELECT id_kota 
          INTO v_kota_id
          FROM kota 
         WHERE nama_kota = p_nama_kota
         LIMIT 1;

        -- Masukkan data ke tabel koperasi
        INSERT INTO koperasi
              (nama_koperasi, alamat, id_kota, simpanan_pokok, simpanan_wajib)
        VALUES (p_nama_koperasi, p_alamat, v_kota_id,
                p_simpanan_pokok, p_simpanan_wajib);
        SET v_koperasi_id = LAST_INSERT_ID();

        -- Masukkan data ke tabel akun
        INSERT INTO akun (id_koperasi, role_admin)
        VALUES (v_koperasi_id, 'Y');
        SET v_akun_id = LAST_INSERT_ID();

        -- Masukkan data ke tabel login
        INSERT INTO login (id_akun, username, password, email)
        VALUES (v_akun_id, p_username, p_password_hash, p_email);
        SET v_login_id = LAST_INSERT_ID();

    COMMIT;

    -- Kembalikan ID yang dihasilkan
    SELECT v_koperasi_id  AS koperasi_id,
           v_akun_id      AS akun_id,
           v_login_id     AS login_id;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_register_user (
    IN p_username     VARCHAR(50),
    IN p_password     VARCHAR(255),
    IN p_role_admin   VARCHAR(1),
    IN p_id_koperasi  INT
)
BEGIN
    DECLARE v_id_akun INT;
    DECLARE v_username_count INT;

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Terjadi kesalahan saat mendaftarkan user.';
    END;

    START TRANSACTION;

        -- Cek apakah username sudah ada
        SELECT COUNT(*) INTO v_username_count
        FROM login
        WHERE username = p_username;

        IF v_username_count > 0 THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username sudah digunakan.';
        END IF;

        -- Insert ke tabel akun
        INSERT INTO akun (role_admin, id_koperasi)
        VALUES (p_role_admin, p_id_koperasi);
        SET v_id_akun = LAST_INSERT_ID();

        -- Insert ke tabel login
        INSERT INTO login (username, password, id_akun)
        VALUES (p_username, p_password, v_id_akun);

    COMMIT;

    -- Kembalikan ID akun
    SELECT v_id_akun AS akun_id;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_update_user (
    IN p_id_akun       INT,
    IN p_username      VARCHAR(50),
    IN p_password      VARCHAR(255),
    IN p_role_admin    VARCHAR(1)
)
BEGIN
    DECLARE v_username_count INT;

    -- Handler error
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Terjadi kesalahan saat memperbarui user.';
    END;

    START TRANSACTION;

        -- Cek apakah username sudah digunakan oleh akun lain
        SELECT COUNT(*) INTO v_username_count
        FROM login
        WHERE username = p_username AND id_akun != p_id_akun;

        IF v_username_count > 0 THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username sudah digunakan oleh akun lain.';
        END IF;

        -- Update tabel akun (hanya role_admin)
        UPDATE akun
        SET role_admin = p_role_admin
        WHERE id_akun = p_id_akun;

        -- Update tabel login
        UPDATE login
        SET username = p_username,
            password = p_password
        WHERE id_akun = p_id_akun;

    COMMIT;

    SELECT 'Data user berhasil diperbarui' AS pesan;
END $$

DELIMITER ;


CREATE VIEW view_profil_koperasi AS
SELECT 
    k.id_koperasi,
    k.nama_koperasi, 
    k.alamat, 
    provinsi.nama_provinsi, 
    kota.nama_kota, 
    k.simpanan_pokok, 
    k.simpanan_wajib
FROM 
    koperasi k
JOIN 
    kota ON k.id_kota = kota.id_kota
JOIN 
    provinsi ON kota.id_provinsi = provinsi.id_provinsi;



DELIMITER //

CREATE FUNCTION AmbilIdCustomTerbaru()
RETURNS VARCHAR(10)
DETERMINISTIC
BEGIN
    DECLARE id VARCHAR(10);

    SELECT anggota.id_custom
    INTO id
    FROM anggota
    WHERE id_anggota = (SELECT MAX(id_anggota) FROM anggota);

    RETURN id;
END;
//

DELIMITER ;

DELIMITER //

CREATE FUNCTION CekIdCustomSudahAda(id VARCHAR(10))
RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    DECLARE jumlah INT;

    SELECT COUNT(*) INTO jumlah
    FROM anggota
    WHERE id_custom = id;

    RETURN jumlah > 0;
END;
//

DELIMITER ;

DELIMITER //

CREATE FUNCTION CekIdCustomSudahAdaKecuali(
    id VARCHAR(10),
    id_dikecualikan INT
)
RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    DECLARE jumlah INT;

    SELECT COUNT(*) INTO jumlah
    FROM anggota
    WHERE id_custom = id AND id_anggota != id_dikecualikan;

    RETURN jumlah > 0;
END;
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_insert_anggota
AFTER INSERT ON anggota
FOR EACH ROW
BEGIN
    DECLARE nilai_pokok INT;

    -- Ambil nilai simpanan_pokok dari koperasi
    SELECT simpanan_pokok INTO nilai_pokok
    FROM koperasi
    WHERE id_koperasi = NEW.id_koperasi;

    -- Tambahkan ke simpanan, lengkap dengan tanggal hari ini
    INSERT INTO simpanan (
        id_koperasi, id_anggota, id_jenis, nilai, keterangan, tanggal
    )
    VALUES (
        NEW.id_koperasi,
        NEW.id_anggota,
        3,
        nilai_pokok,
        'simpanan pokok anggota baru',
        CURDATE()
    );
END;
//

DELIMITER ;

DELIMITER $$

CREATE TRIGGER after_insert_pelunasan
AFTER INSERT ON pelunasan
FOR EACH ROW
BEGIN
  UPDATE pinjaman
  SET 
    sisa = sisa - NEW.nilai,
    pelunasan_terakhir = NEW.angsuran_ke
  WHERE id_pinjaman = NEW.id_pinjaman;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER after_delete_pelunasan
AFTER DELETE ON pelunasan
FOR EACH ROW
BEGIN
  UPDATE pinjaman
  SET 
    sisa = sisa + OLD.nilai,
    pelunasan_terakhir = 
      CASE 
        WHEN pelunasan_terakhir > OLD.angsuran_ke THEN pelunasan_terakhir - 1
        ELSE pelunasan_terakhir
      END
  WHERE id_pinjaman = OLD.id_pinjaman;
END$$

DELIMITER ;

CREATE VIEW view_total_simpanan AS
SELECT
  s.id_koperasi,
  SUM(CASE WHEN j.id_jenis = 3 THEN s.nilai ELSE 0 END) AS total_pokok,
  SUM(CASE WHEN j.id_jenis = 2 THEN s.nilai ELSE 0 END) AS total_wajib,
  SUM(CASE WHEN j.id_jenis = 1 THEN s.nilai ELSE 0 END) AS total_sukarela
FROM simpanan s
JOIN jenis_simpanan j ON s.id_jenis = j.id_jenis
GROUP BY s.id_koperasi;

CREATE VIEW view_total_anggota_per_koperasi AS
SELECT 
    id_koperasi,
    SUM(CASE WHEN jenis_kelamin = 'Laki-Laki' THEN 1 ELSE 0 END) AS total_laki,
    SUM(CASE WHEN jenis_kelamin = 'Perempuan' THEN 1 ELSE 0 END) AS total_perempuan
FROM anggota
GROUP BY id_koperasi;

CREATE VIEW view_total_simpanan_per_anggota AS
SELECT
    s.id_koperasi,
    SUM(CASE WHEN j.id_jenis = 3 THEN s.nilai ELSE 0 END) AS total_pokok,
    SUM(CASE WHEN j.id_jenis = 2 THEN s.nilai ELSE 0 END) AS total_wajib,
    MAX(CASE WHEN j.id_jenis = 2 THEN s.tanggal ELSE NULL END) AS tanggal_terakhir_wajib,
    SUM(CASE WHEN j.id_jenis = 1 THEN s.nilai ELSE 0 END) AS total_sukarela,
    a.id_custom AS id_custom_anggota,
    a.nama AS nama_anggota
FROM simpanan s
JOIN jenis_simpanan j ON s.id_jenis = j.id_jenis
JOIN anggota a ON s.id_anggota = a.id_anggota
GROUP BY s.id_anggota, s.id_koperasi;

CREATE VIEW view_ringkasan_pinjaman AS
SELECT
    p.id_koperasi,
    p.tgl_cair,
    p.id_pinjaman_custom,
    p.angsuran,
    p.nilai_pinjaman,
    a.id_custom AS id_custom_anggota,
    a.nama AS nama_anggota,
    j.persen,
    p.nilai_pokok_angsuran,
    p.pelunasan_terakhir,
    p.sisa,
    MAX(pl.tanggal) AS tanggal_terakhir_pelunasan
FROM pinjaman p
JOIN anggota a ON p.id_anggota = a.id_anggota
JOIN jasa j ON p.id_jasa = j.id_jasa AND p.id_koperasi = j.id_koperasi
JOIN pelunasan pl ON p.id_pinjaman = pl.id_pinjaman
GROUP BY p.id_koperasi, p.id_pinjaman_custom, p.id_anggota;

CREATE VIEW view_laporan_pelunasan AS
SELECT
    pl.id_koperasi,
    p.id_pinjaman_custom,
    pl.angsuran_ke,
    pl.nilai,
    pl.tanggal,
    pl.id_pelunasan
FROM pelunasan pl
JOIN pinjaman p ON pl.id_pinjaman = p.id_pinjaman;

CREATE VIEW view_laporan_simpanan AS
SELECT
    s.id_koperasi,
    a.id_custom AS id_custom_anggota,
    a.nama AS nama_anggota,
    j.jenis_simpanan,
    s.nilai,
    s.tanggal,
    s.keterangan,
    s.id_simpanan
FROM simpanan s
JOIN anggota a ON s.id_anggota = a.id_anggota
JOIN jenis_simpanan j ON s.id_jenis = j.id_jenis;