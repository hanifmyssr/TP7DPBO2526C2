# TP7DPBO2526C2

## Janji
Saya Muhammad Hanif Muyassar dengan NIM 2510593 mengerjakan Tugas Praktikum 7 dalam mata kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahanNya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin.


## Tema Website

GymHub adalah sistem pengelolaan keanggotaan sebuah pusat kebugaran. Aplikasi ini membantu petugas gym untuk:

- mencatat data **member** yang mendaftar,
- mengelola daftar **paket keanggotaan** (misalnya Daily, Monthly, 3 Months, Yearly) beserta harga dan durasinya,
- mencatat **keanggotaan**, yaitu transaksi ketika seorang member membeli suatu paket pada tanggal tertentu.

Setiap tabel memiliki fitur CRUD lengkap (Create, Read, Update, Delete) yang dapat dijalankan dari antarmuka web.

### Aturan bisnis

- Satu member boleh memiliki banyak keanggotaan sebagai riwayat, tetapi **hanya satu keanggotaan aktif pada satu waktu**. Keanggotaan dianggap aktif jika berstatus `active` dan `end_date` belum lewat. Member yang masih aktif tidak dapat membuat keanggotaan baru sebelum yang lama berakhir atau dibatalkan (status `cancelled`).
- `end_date` dihitung otomatis di PHP: `start_date` ditambah `duration_days` dari paket.
- `total_paid` disalin dari harga paket **pada saat transaksi** dan disimpan permanen. Jika harga paket diubah kemudian, riwayat pembayaran lama tidak ikut berubah.
- Update pada keanggotaan hanya mengubah **status** (`active`, `expired`, `cancelled`). Mengubah paket atau tanggal berarti harus menghitung ulang `end_date` dan `total_paid`, sehingga dibuat sebagai pengembangan lanjutan.
- Email member bersifat unik.
- Member atau paket yang masih dipakai oleh data keanggotaan **tidak dapat dihapus** (dijaga oleh foreign key), dan aplikasi menampilkan pesan yang jelas.


## Database

Nama database: `db_gymhub` (file: `database/db_gymhub.sql`, berisi struktur tabel dan data contoh).

### Relasi antar tabel

```
members (1) ----< (banyak) memberships (banyak) >---- (1) packages
```

`memberships` adalah tabel transaksi yang menghubungkan `members` dan `packages`, sehingga satu member dapat memiliki banyak keanggotaan, dan satu paket dapat dibeli oleh banyak member.

### Tabel `members`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) | Nama member, wajib |
| phone | VARCHAR(15) | Nomor HP, wajib |
| email | VARCHAR(100) | Email, wajib dan unik |
| join_date | DATE | Tanggal daftar, diisi otomatis dengan `CURDATE()` |

### Tabel `packages`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, AUTO_INCREMENT | Primary key |
| package_name | VARCHAR(100) | Nama paket, wajib |
| price | DECIMAL(10,2) | Harga paket, wajib |
| duration_days | INT | Lama paket dalam hari, wajib |

### Tabel `memberships`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT, AUTO_INCREMENT | Primary key |
| member_id | INT | Foreign key ke `members(id)` |
| package_id | INT | Foreign key ke `packages(id)` |
| start_date | DATE | Tanggal mulai |
| end_date | DATE | Tanggal berakhir, dihitung di PHP |
| total_paid | DECIMAL(12,2) | Harga saat transaksi, disimpan permanen |
| status | ENUM('active','expired','cancelled') | Default `active` |


## Struktur Folder

```
TP7DPBO2526C2/
├── class/
│   ├── Member.php
│   ├── Package.php
│   └── Membership.php
├── config/
│   └── db.php
├── database/
│   └── db_gymhub.sql
├── dokumentasi/
│   └── TP7doc.mp4
├── view/
│   ├── header.php
│   ├── footer.php
│   ├── members.php
│   ├── packages.php
│   └── memberships.php
├── index.php
├── style.css
└── README.md
```

| Folder/File | Fungsi |
|---|---|
| `class/` | Satu class untuk setiap tabel. Berisi seluruh query database (CRUD) memakai prepared statement. |
| `config/db.php` | Class `Database` yang membuat koneksi PDO ke MySQL. Hanya di sini konfigurasi koneksi ditulis. |
| `database/` | File SQL struktur dan data contoh. |
| `view/` | Tampilan. `header.php` dan `footer.php` dipakai di semua halaman, sedangkan tiga file lain adalah halaman tiap tabel. |
| `index.php` | Titik masuk aplikasi: memuat class, memproses form, lalu menampilkan halaman. |
| `style.css` | Tampilan halaman. |


## Flow Program

### Alur umum (Request - Response)

1. Pengguna membuka halaman di browser, misalnya `index.php?page=members`.
2. `index.php` memuat ketiga class dan membuat objeknya (`$member`, `$package`, `$membership`).
3. Jika ada form yang dikirim (`$_POST`), `index.php` memvalidasi input, lalu memanggil method class yang sesuai.
4. Class menjalankan query ke database melalui koneksi PDO dari `config/db.php`.
5. Hasilnya (pesan sukses atau error) disimpan di variabel, lalu `index.php` memuat file view sesuai `?page=...`.
6. View menampilkan tabel dan form, dan browser menerima HTML hasilnya.

### Koneksi database (`config/db.php`)

Class `Database` membuat koneksi `new PDO(...)` di dalam constructor dan menyimpannya di properti `$conn`. Mode error diatur ke `ERRMODE_EXCEPTION` agar error database dilempar sebagai exception dan dapat ditangkap dengan `try/catch`. Setiap class mengambil koneksinya dengan:

```php
$this->db = (new Database())->conn;
```

Karena koneksi dipusatkan di satu class, pergantian database cukup dilakukan di constructor ini tanpa mengubah class lain.

### Prepared statement

Seluruh query memakai pola `prepare()`, lalu `execute()`, termasuk query tanpa parameter. Tidak ada pemanggilan `query()` langsung. Contoh:

```php
$stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
$stmt->execute([$id]);
```

Data dikirim terpisah dari perintah SQL, sehingga aman dari SQL injection.

### Method pada tiap class

| Class | Method |
|---|---|
| `Member` | `getAllMembers`, `createMember`, `updateMember`, `deleteMember` |
| `Package` | `getAllPackages`, `createPackage`, `updatePackage`, `deletePackage` |
| `Membership` | `getAllMemberships` (JOIN ke `members` dan `packages`), `hasActiveMembership`, `createMembership`, `updateMembership`, `deleteMembership` |

Untuk mengisi form Edit, `index.php` memakai fungsi bantu `findById()` yang mencari baris berdasarkan id dari hasil `getAll...()`, sehingga tidak ada method `getById` tambahan di class. Pada aplikasi dengan data besar, query `WHERE id = ?` akan lebih efisien.

### Alur tiap operasi

**Read.** View memanggil `getAll...()` dan menampilkan hasilnya dengan `foreach`. Halaman Memberships memakai query JOIN sehingga yang tampil nama member dan nama paket, bukan angka id.

**Create Member.** Form dikirim dengan tombol `add_member`. `index.php` memangkas spasi (`trim`), memeriksa semua kolom terisi dan format email valid, lalu memanggil `createMember()`. Jika email sudah ada, database menolaknya (kode error 1062), class mengembalikan `'duplicate'`, dan `index.php` menampilkan pesan "Email sudah terdaftar".

**Create Package.** Sama seperti Member, ditambah validasi bahwa harga berupa angka lebih dari 0 dan durasi berupa bilangan bulat lebih dari 0.

**Create Membership.**
1. Memeriksa member, paket, dan tanggal mulai terisi.
2. Memanggil `hasActiveMembership()`. Jika member masih punya keanggotaan aktif, proses ditolak.
3. Mengambil harga dan durasi paket, lalu `createMembership()` menghitung `end_date` dan menyimpan harga sebagai `total_paid`.

**Update.**
- Member dan Package: klik Edit, `?edit=ID` membuat form terisi data lama, lalu tombol Update memanggil `updateMember()` atau `updatePackage()`. Email yang sudah dipakai member lain ditolak (kode 1062).
- Membership: hanya status yang dapat diubah lewat dropdown.

**Delete.** Tombol Delete mengirim form POST dengan konfirmasi `confirm()`. Jika data masih dipakai tabel lain (foreign key, kode 1451), class mengembalikan `'in use'` dan `index.php` menampilkan pesan bahwa data tidak dapat dihapus. Hapus Membership tidak memiliki pembatasan karena tidak ada tabel yang merujuknya.

### Penanganan error

| Kode MySQL | Penyebab | Penanganan |
|---|---|---|
| 1062 | Email duplikat | Pesan "Email sudah terdaftar" |
| 1451 | Data masih dipakai tabel lain | Pesan "tidak dapat dihapus" |
| 1452 | Member atau paket tidak ada | Pesan "tidak valid" |

Error jenis lain dilempar ulang (`throw $e`) agar tidak tersembunyi saat pengembangan.

### Keamanan

- Prepared statement untuk seluruh query.
- Output ke HTML dibungkus `htmlspecialchars()`.
- Validasi input di sisi server.
- Daftar halaman dibatasi (whitelist) sebelum di-`include`.


## Dokumentasi

https://github.com/user-attachments/assets/ad3658b5-10a2-497c-b817-329e57ab67cf



