# Tugas Rutin 7 — Sistem Login/Register (PHP Native + JSON)

Sistem login/register sederhana menggunakan PHP Native, dengan file JSON (`data/users.json`) sebagai penyimpanan data.

## Cara Menjalankan

1. Pastikan PHP sudah terpasang (minimal PHP 7.4+, disarankan PHP 8.x).
2. Buka terminal di folder proyek ini, lalu jalankan:
   ```
   php -S localhost:8000
   ```
3. Buka browser ke `http://localhost:8000`.
4. Pastikan folder `data/` memiliki izin tulis (writable) agar file `users.json` bisa diperbarui.

## Struktur Folder

```
tugas-rutin-7/
├── index.php            # Redirect otomatis ke login/dashboard
├── register.php         # Halaman registrasi
├── login.php            # Halaman login (+ Remember Me)
├── dashboard.php         # Halaman dashboard (diproteksi)
├── edit_profile.php     # Bonus: edit nama/email/password
├── logout.php           # Proses logout (session_destroy)
├── config.php           # Konfigurasi & inisialisasi session
├── includes/
│   └── functions.php    # Fungsi bantu (baca/tulis JSON, validasi, auth)
├── data/
│   └── users.json       # Penyimpanan data user (auto-dibuat)
└── assets/
    └── style.css        # Styling halaman
```

## Checklist Requirement

- ✅ Form registrasi dengan validasi (nama, email, password)
- ✅ Validasi email dengan `filter_var()`
- ✅ Password di-hash dengan `password_hash()`
- ✅ Data disimpan di file JSON
- ✅ Cek duplikasi email saat registrasi
- ✅ Sistem login dengan session
- ✅ Dashboard yang diproteksi (redirect ke login jika belum login)
- ✅ Logout functionality (`session_destroy()`)
- ✅ Sanitasi input dengan `htmlspecialchars()`
- ✅ Pesan error & sukses yang jelas (alert box + flash message)

## Bonus yang Diimplementasikan

- ⭐ **Remember Me**: menyimpan token acak di cookie (30 hari) yang divalidasi di `requireLogin()` untuk auto-login.
- ⭐ **Edit Profile**: halaman `edit_profile.php` untuk mengubah nama, email, dan password.
- ⭐ **Tampilan CSS rapi**: desain kartu modern dengan gradient, responsive untuk mobile.

## Catatan Keamanan

- Password disimpan dalam bentuk hash (bcrypt via `password_hash`), tidak pernah plaintext.
- Semua output ke HTML di-escape dengan `htmlspecialchars()` untuk mencegah XSS.
- Remember token dihapus dari data user saat logout agar cookie lama tidak bisa dipakai lagi.
