# Spa Reservation Site (PHP + MySQL)
1) Create a MySQL database and import `_schema.sql`.
2) Edit `config.php` with your DB credentials and WhatsApp number.
3) Upload the whole folder to your hosting (InfinityFree, cPanel, etc.).
4) Visit `/admin/login.php` (admin/admin123 default). Change the password ASAP.

Notes:
- Reservations are saved to DB, then redirected to WhatsApp chat with formatted details.
- Upload service images in Admin > Services. Hero image and About content in Admin > Settings.
- File uploads are stored in `/uploads`.
