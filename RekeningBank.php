<?php
declare(strict_types=1);

/**
 * Sesi 3 — PHP tidak punya constructor overloading.
 * Padanannya: default parameter + named constructor (static factory).
 */
class RekeningBank
{
    public const BUNGA_TAHUNAN = 0.025;
    public const BIAYA_ADMINISTRASI = 5000;
    public const BATAS_PENARIKAN_SEKALI = 5000000;

    private static int $jumlahRekening = 0;

    private float $saldo;

    public function __construct(
        private readonly string $nomor,
        private readonly string $pemilik,
        float $saldoAwal = 0,
    ) {
        if (trim($nomor) === '') {
            throw new InvalidArgumentException('Nomor rekening tidak boleh kosong');
        }
        if ($saldoAwal < 0) {
            throw new InvalidArgumentException('Saldo awal tidak boleh negatif');
        }

        $this->saldo = $saldoAwal;
        self::$jumlahRekening++;
    }

    /** Named constructor — rekening pelajar, saldo awal nol. */
    public static function rekeningPelajar(string $nomor, string $pemilik): static
    {
        return new static($nomor, $pemilik, 0);
    }

    public function setor(float $jumlah): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException('Jumlah setoran harus positif');
        }
        $this->saldo += $jumlah;
    }

    public function tarik(float $jumlah): void
    {
        if ($jumlah <= 0) {
            throw new InvalidArgumentException('Jumlah penarikan harus positif');
        }
        if ($jumlah > $this->saldo) {
            throw new InvalidArgumentException('Saldo tidak mencukupi');
        }
        if ($jumlah > self::BATAS_PENARIKAN_SEKALI) {
            throw new InvalidArgumentException('Melebihi batas penarikan per transaksi');
        }
        $this->saldo -= $jumlah;
    }

    public function potongBiayaAdmin(): void
    {
        $this->saldo = max(0, $this->saldo - self::BIAYA_ADMINISTRASI);
    }

    public static function getJumlahRekening(): int
    {
        return self::$jumlahRekening;
    }

    public static function bungaSetahun(float $pokok): float
    {
        return $pokok * self::BUNGA_TAHUNAN;
    }

    public function getSaldo(): float { return $this->saldo; }
    public function getNomor(): string { return $this->nomor; }

    public function __toString(): string
    {
        return sprintf('Rekening[%s] %-14s Rp%s',
            $this->nomor, $this->pemilik, number_format($this->saldo, 2, ',', '.'));
    }
}