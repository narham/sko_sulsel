<?php

namespace App\Controllers\Admin;

use App\Models\SiswaModel;
use App\Models\KelasModel;

use App\Controllers\BaseController;
use App\Models\JurusanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataSiswa extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;
   protected JurusanModel $jurusanModel;

   protected $siswaValidationRules = [
      'nik' => [
         'rules' => 'required|max_length[16]|min_length[16]',
         'errors' => [
            'required' => 'NIK harus diisi.',
            'is_unique' => 'NIK ini telah terdaftar.',
            'min_length[4]' => 'Panjang NIS minimal 4 karakter'
         ]
      ],
      'nis' => [
         'rules' => 'required|max_length[20]|min_length[4]',
         'errors' => [
            'required' => 'NIS harus diisi.',
            'is_unique' => 'NIS ini telah terdaftar.',
            'min_length[4]' => 'Panjang NIS minimal 4 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_kelas' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Kelas harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();
      $this->jurusanModel = new JurusanModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Siswa',
         'ctx' => 'siswa',
         'kelas' => $this->kelasModel->getAllKelas(),
         'jurusan' => $this->jurusanModel->findAll()
      ];

      return view('admin/data/data-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      $kelas = $this->request->getVar('kelas') ?? null;
      $jurusan = $this->request->getVar('jurusan') ?? null;

      $result = $this->siswaModel->getAllSiswaWithKelas($kelas, $jurusan);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-siswa', $data);
   }

   public function formTambahSiswa()
   {
      $kelas = $this->kelasModel->getAllKelas();

      $data = [
         'ctx' => 'siswa',
         'kelas' => $kelas,
         'title' => 'Tambah Data Siswa'
      ];

      return view('admin/data/create/create-data-siswa', $data);
   }

   public function saveSiswa()
   {
      // validasi
      if (!$this->validate($this->siswaValidationRules)) {
         $kelas = $this->kelasModel->getAllKelas();

         $data = [
            'ctx' => 'siswa',
            'kelas' => $kelas,
            'title' => 'Tambah Data Siswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-siswa', $data);
      }

      $nis = $this->request->getVar('nis');
      $nik = $this->request->getVar('nik');
      $namsiswa = $this->request->getVar('nama');
      $tlahir = $this->request->getVar('tlahir');
      $tglahir = $this->request->getVar('tglahir');
      $idkelas = intval($this->request->getVar('id_kelas'));
      $jeniskelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->siswaModel->saveSiswa(NULL, $nis, $nik, $namsiswa, $tlahir, $tglahir, $idkelas, $jeniskelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/siswa/create');
   }

   public function formEditSiswa($id)
   {
      $siswa = $this->siswaModel->getSiswaById($id);
      $kelas = $this->kelasModel->getAllKelas();

      if (empty($siswa) || empty($kelas)) {
         throw new PageNotFoundException('Data siswa dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $siswa,
         'kelas' => $kelas,
         'ctx' => 'siswa',
         'title' => 'Edit Siswa',
      ];

      return view('admin/data/edit/edit-data-siswa', $data);
   }

   public function updateSiswa()
   {
      $idsiswa = $this->request->getVar('id');

      $siswalama = $this->siswaModel->getSiswaById($idsiswa);

      if ($siswalama['nis'] != $this->request->getVar('nis')) {
         $this->siswaValidationRules['nis']['rules'] = 'required|max_length[20]|min_length[4]|is_unique[tb_siswa.nis]';
      }

      // validasi
      if (!$this->validate($this->siswaValidationRules)) {
         $siswa = $this->siswaModel->getSiswaById($idsiswa);
         $kelas = $this->kelasModel->getAllKelas();

         $data = [
            'data' => $siswa,
            'kelas' => $kelas,
            'ctx' => 'siswa',
            'title' => 'Edit Siswa',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-siswa', $data);
      }

      $nik = $this->request->getVar('nik');
      $nis = $this->request->getVar('nis');
      $namasiswa = $this->request->getVar('nama');
      $tlahir = $this->request->getVar('tlahir');
      $tglahir = $this->request->getVar('tglahir');
      $idkelas = intval($this->request->getVar('id_kelas'));
      $jeniskelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->siswaModel->saveSiswa($idsiswa, $nis, $nik, $namasiswa, $tlahir, $tglahir, $idkelas, $jeniskelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/siswa/edit/' . $idsiswa);
   }

   public function delete($id)
   {
      $result = $this->siswaModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/siswa');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/siswa');
   }
}
