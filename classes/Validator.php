<?php
/**
 * Class Validator
 * 
 * Mengelola semua validasi input pengguna.
 * Menerapkan konsep Static Method karena utility class
 * tidak perlu diinstansiasi berulang kali.
 * 
 * Point: Pemenuhan CPMK093 (Validasi Input)
 */
class Validator
{
    /**
     * Memvalidasi nilai stok (tidak boleh negatif)
     * CPMK093: menolak input jika pengguna mencoba memasukkan nilai stok minus/negatif
     * 
     * @param mixed $stok Nilai stok yang diinputkan
     * @return bool True jika valid (>= 0), False jika tidak valid (< 0 atau bukan angka)
     */
    public static function validateStok($stok)
    {
        // Pastikan input berupa angka atau string angka
        if (!is_numeric($stok)) {
            return false;
        }

        // Cast ke integer
        $stokInt = (int)$stok;

        // Cek apakah negatif
        if ($stokInt < 0) {
            return false;
        }

        return true;
    }

    /**
     * Sanitasi input string untuk mencegah XSS (Cross-Site Scripting)
     * 
     * @param string $data Data input dari form
     * @return string Data yang sudah disanitasi
     */
    public static function sanitize($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * Validasi field-field wajib diisi
     * 
     * @param array $fields Array associative [nama_field => nilai]
     * @return array Array berisi pesan error jika ada field yang kosong
     */
    public static function validateRequired($fields)
    {
        $errors = [];
        foreach ($fields as $fieldName => $value) {
            if (empty(trim($value)) && $value !== '0' && $value !== 0) {
                $errors[] = "Field " . str_replace('_', ' ', ucfirst($fieldName)) . " wajib diisi.";
            }
        }
        return $errors;
    }

    /**
     * Memvalidasi harga (harus angka positif > 0)
     * 
     * @param mixed $harga Nilai harga
     * @return bool True jika valid, False jika tidak
     */
    public static function validateHarga($harga)
    {
        if (!is_numeric($harga)) {
            return false;
        }

        if ((float)$harga <= 0) {
            return false;
        }

        return true;
    }
}
