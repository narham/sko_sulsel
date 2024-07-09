<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'nis', // NIS
         'nik', // NIK
         'nama_siswa', // Nama Lengkap
         'tlahir', // Tempat Lahir
         'tglahir', // Tanggal Lahir
         'id_kelas', // ID Kelas
         'jenis_kelamin', // Jenis Kelamain
         'no_hp', // No Handphone
         'unique_code' // Unique Code 
      ];
   }

   protected $table = 'tb_siswa';

   protected $primaryKey = 'id_siswa';

   public function cekSiswa(string $unique_code)
   {
      $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_jurusan.id = tb_kelas.id_jurusan',
         'LEFT'
      );
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getSiswaById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllSiswaWithKelas($kelas = null, $jurusan = null)
   {
      $query = $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_kelas.id_jurusan = tb_jurusan.id',
         'LEFT'
      );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas, 'jurusan' => $jurusan]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_siswa')->findAll();
   }

   public function getSiswaByKelas($id_kelas)
   {
      return $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'RIGHT'
      )
         ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
         ->where(['tb_siswa.id_kelas' => $id_kelas])->findAll();
   }

   public function saveSiswa($idsiswa, $nis, $nik, $namasiswa, $tlahir, $tglahir, $idkelas, $jeniskelamin, $noHp)
   {
      return $this->save([
         $this->primaryKey => $idsiswa,
         'nis' => $nis,
         'nik' => $nik,
         'nama_siswa' => $namasiswa,
         'tlahir' => $tlahir, // Tempat Lahir
         'tglahir' => $tglahir, //Tanggal Lahir
         'id_kelas' => $idkelas, // ID Kelas
         'jenis_kelamin' => $jeniskelamin,
         'no_hp' => $noHp,
         'unique_code' => sha1($namasiswa . md5($nik . $tlahir . $namasiswa)) . substr(sha1($nik . $tglahir . rand(0, 100)), 0, 24)
      ]);
   }
}
