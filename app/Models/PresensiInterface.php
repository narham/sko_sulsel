<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

/**
 * interface PresensiInterface
 * @method cekAbsen
 * @method absenMasuk
 * @method absenKeluar
 * @method getPresensiById
 * @method getPresensiByKehadiran
 * @method updatePresensi
 */
interface PresensiInterface
{
  public function cekAbsen(string|int $id, string|Time $date);
  public function absenMasuk(string $id, $date, $time);
  public function absenKeluar(string $id, $time);
  public function getPresensiById(string $idpresensi);
  public function getPresensiByKehadiran(string $idkehadiran, $tanggal);
}
