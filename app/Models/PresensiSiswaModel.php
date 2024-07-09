<?php

namespace App\Models;

use App\Models\PresensiInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Libraries\enums\Kehadiran;

class PresensiSiswaModel extends Model implements PresensiInterface

{
   protected $primaryKey = 'id_presensi';

   protected $allowedFields = [
      'id_siswa',
      'id_kelas',
      'tanggal',
      'jam_masuk',
      'jam_keluar',
      'id_kehadiran',
      'keterangan'
   ];

   protected $table = 'tb_presensi_siswa';

   public function cekAbsen(string|int $id, string|Time $date)
   {
      $result = $this->where(['id_siswa' => $id, 'tanggal' => $date])->first();

      if (empty($result)) return false;

      return $result[$this->primaryKey];
   }

   public function absenMasuk(string $id,  $date, $time, $idkelas = '')
   {
      $this->save([
         'id_siswa' => $id,
         'id_kelas' => $idkelas,
         'tanggal' => $date,
         'jam_masuk' => $time,
         // 'jam_keluar' => '',
         'id_kehadiran' => Kehadiran::Hadir->value,
         'keterangan' => ''
      ]);
   }

   public function absenKeluar(string $id, $time)
   {
      $this->update($id, [
         'jam_keluar' => $time,
         'keterangan' => ''
      ]);
   }

   public function getPresensiByIdSiswaTanggal($idsiswa, $date)
   {
      return $this->where(['id_siswa' => $idsiswa, 'tanggal' => $date])->first();
   }

   public function getPresensiById(string $idPresensi)
   {
      return $this->where([$this->primaryKey => $idPresensi])->first();
   }

   public function getPresensiByKelasTanggal($idkelas, $tanggal)
   {
      return $this->setTable('tb_siswa')
         ->select('*')
         ->join(
            "(SELECT id_presensi, id_siswa AS id_siswa_presensi, tanggal, jam_masuk, jam_keluar, id_kehadiran, keterangan FROM tb_presensi_siswa)tb_presensi_siswa",
            "{$this->table}.id_siswa = tb_presensi_siswa.id_siswa_presensi AND tb_presensi_siswa.tanggal = '$tanggal'",
            'LEFT'
         )
         ->join(
            'tb_kehadiran',
            'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
            'LEFT'
         )
         ->where("{$this->table}.id_kelas = $idkelas")
         ->orderBy("id_siswa")
         // ->find();
         ->findAll();
   }

   public function getPresensiByKehadiran(string $idkehadiran, $tanggal)
   {
      $this->join(
         'tb_siswa',
         "tb_presensi_siswa.id_siswa = tb_siswa.id_siswa AND tb_presensi_siswa.tanggal = '$tanggal'",
         'right'
      );
      if ($idkehadiran == '4') {
         $result = $this->findAll();

         $filteredResult = [];

         foreach ($result as $value) {
            if ($value['id_kehadiran'] != ('1' || '2' || '3')) {
               array_push($filteredResult, $value);
            }
         }

         return $filteredResult;
      } else {
         $this->where(['tb_presensi_siswa.id_kehadiran' => $idkehadiran]);
         return $this->findAll();
      }
   }

   public function updatePresensi(
      $idpresensi,
      $idsiswa,
      $idkelas,
      $tanggal,
      $idkehadiran,
      $jammasuk,
      $jamkeluar,
      $keterangan
   ) {
      $presensi = $this->getPresensiByIdSiswaTanggal($idsiswa, $tanggal);

      $data = [
         'id_siswa' => $idsiswa,
         'id_kelas' => $idkelas,
         'tanggal' => $tanggal,
         'id_kehadiran' => $idkehadiran,
         'keterangan' => $keterangan ?? $presensi['keterangan'] ?? ''
      ];

      if ($idpresensi != null) {
         $data[$this->primaryKey] = $idpresensi;
      }

      if ($jammasuk != null) {
         $data['jam_masuk'] = $jammasuk;
      }

      if ($jamkeluar != null) {
         $data['jam_keluar'] = $jamkeluar;
      }

      return $this->save($data);
   }
}
