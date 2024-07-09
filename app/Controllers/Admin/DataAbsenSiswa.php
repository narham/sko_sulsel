<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;

use App\Models\SiswaModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class DataAbsenSiswa extends BaseController
{
   protected KelasModel $kelasModel;

   protected SiswaModel $siswaModel;

   protected KehadiranModel $kehadiranModel;

   protected PresensiSiswaModel $presensiSiswa;

   protected string $currentDate;

   public function __construct()
   {
      $this->currentDate = Time::today()->toDateString();

      $this->siswaModel = new SiswaModel();

      $this->kehadiranModel = new KehadiranModel();

      $this->kelasModel = new KelasModel();

      $this->presensiSiswa = new PresensiSiswaModel();
   }

   public function index()
   {
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'title' => 'Data Absen Siswa',
         'ctx' => 'absen-siswa',
         'kelas' => $kelas
      ];

      return view('admin/absen/absen-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      // ambil variabel POST
      $kelas = $this->request->getVar('kelas');
      $idkelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiSiswa->getPresensiByKelasTanggal($idkelas, $tanggal);

      $data = [
         'kelas' => $kelas,
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-siswa', $data);
   }

   public function ambilKehadiran()
   {
      $idpresensi = $this->request->getVar('id_presensi');
      $idsiswa = $this->request->getVar('id_siswa');

      $data = [
         'presensi' => $this->presensiSiswa->getPresensiById($idpresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->siswaModel->getSiswaById($idsiswa)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idkehadiran = $this->request->getVar('id_kehadiran');
      $idsiswa = $this->request->getVar('id_siswa');
      $idkelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');
      $jammasuk = $this->request->getVar('jam_masuk');
      $jamkeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiSiswa->cekAbsen($idsiswa, $tanggal);

      $result = $this->presensiSiswa->updatePresensi(
         $cek == false ? NULL : $cek,
         $idsiswa,
         $idkelas,
         $tanggal,
         $idkehadiran,
         $jammasuk ?? NULL,
         $jamkeluar ?? NULL,
         $keterangan
      );

      $response['nama_siswa'] = $this->siswaModel->getSiswaById($idsiswa)['nama_siswa'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
