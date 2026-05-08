## Fitur Deteksi Stok Menipis (Otomatis)

Sistem inventaris ini memiliki fitur pendeteksi otomatis yang akan memunculkan peringatan **"STOK MENIPIS!"** pada halaman Dashboard apabila jumlah stok suatu barang berada di bawah 5 unit. Berikut adalah demonstrasi alur kerja fitur tersebut:

### Screenshot 1 — Dashboard Menampilkan Peringatan Stok Menipis

Pada halaman Dashboard, sistem secara otomatis mendeteksi dan menampilkan alert berwarna merah bertuliskan **"PERINGATAN: STOK MENIPIS!"**. Terdapat 4 barang yang stoknya di bawah 5 unit, salah satunya adalah produk **Vivo V30 Pro** dengan sisa stok hanya **1 unit**. Produk lainnya yang juga masuk peringatan adalah iPhone 15 Pro Max (2 unit), HP Pavilion 15 (3 unit), dan Dell XPS 13 (4 unit). Peringatan ini berfungsi sebagai notifikasi bagi admin toko agar segera melakukan restock barang sebelum kehabisan.

<img width="1917" height="911" alt="Screenshot 2026-05-08 090112" src="https://github.com/user-attachments/assets/828992f4-9588-4753-aaa5-17cf91bd7790" />

### Screenshot 2 — Proses Edit Stok Produk Vivo V30 Pro

Untuk mengatasi peringatan stok menipis pada Vivo V30 Pro, admin membuka halaman **Edit Produk** melalui menu Kelola Produk. Pada form edit, terlihat data lengkap produk Vivo V30 Pro beserta spesifikasi teknisnya (Processor: MediaTek Dimensity 8200, RAM: 12 GB, Storage: 256 GB, Kamera: 50 MP + 50 MP + 8 MP). Admin kemudian mengubah nilai **Stok Awal** dari 1 menjadi **5 unit**, lalu menekan tombol **"Update Produk"** untuk menyimpan perubahan ke database.

<img width="1896" height="887" alt="Screenshot 2026-05-08 090406" src="https://github.com/user-attachments/assets/070c1dc5-96a1-4736-80fb-a6648dec7599" />

### Screenshot 3 — Dashboard Setelah Stok Diperbarui

Setelah proses update stok berhasil, admin kembali ke halaman Dashboard. Terlihat bahwa produk **Vivo V30 Pro sudah tidak muncul lagi** dalam daftar peringatan stok menipis. Hal ini membuktikan bahwa fitur deteksi stok menipis bekerja secara **real-time dan otomatis** — ketika stok suatu barang sudah mencapai 5 unit atau lebih, maka barang tersebut otomatis keluar dari daftar peringatan. Saat ini hanya tersisa 3 barang yang masih berstatus stok menipis yaitu iPhone 15 Pro Max, HP Pavilion 15, dan Dell XPS 13.

<img width="1918" height="912" alt="Screenshot 2026-05-08 090438" src="https://github.com/user-attachments/assets/ecfac4fd-b20e-4e46-8b6f-64b88e3618e7" />
