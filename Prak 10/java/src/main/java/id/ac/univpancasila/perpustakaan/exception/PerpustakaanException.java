package id.ac.univpancasila.perpustakaan.exception;

/**
 * Sesi 10 — induk seluruh exception domain perpustakaan.
 *
 * Manfaat punya induk bersama: pemanggil bisa menangkap SELURUH kegagalan
 * domain sekaligus dengan satu blok catch, tanpa ikut menelan kesalahan
 * pemrograman yang lain.
 *
 * Keputusan: diturunkan dari RuntimeException (unchecked).
 * Alasan: kegagalan domain di sini (anggota/buku tidak ada, stok habis, batas
 * pinjam tercapai) menandakan permintaan yang tidak sah, bukan keadaan luar
 * yang wajar seperti jaringan atau berkas hilang. Dengan unchecked, setiap
 * method tidak dipaksa memakai throws/try-catch; penanganan cukup dilakukan
 * di satu lapisan yang tepat. Uraian lengkap ada di skenario-uji.md.
 */
public abstract class PerpustakaanException extends RuntimeException {

    protected PerpustakaanException(String pesan) {
        super(pesan);
    }

    protected PerpustakaanException(String pesan, Throwable penyebab) {
        super(pesan, penyebab);
    }
}
