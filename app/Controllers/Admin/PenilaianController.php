<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\KelasModel;
use App\Models\SiswaModel;

class PenilaianController extends BaseController
{
    protected KelasModel $kelasModel;

    protected SiswaModel $siswaModel;
    public function __construct()
    {

        $this->siswaModel = new SiswaModel();

        $this->kelasModel = new KelasModel();
    }
    public function index()
    {
        // Menampilkan daftar Penilaian SIswa
        $kelas = $this->kelasModel->getAllKelas();

        $data = [
            'title' => 'Data Nilai Siswa',
            'ctx' => 'penilaian',
            'kelas' => $kelas
        ];

        return view('admin/penilaian/penilaian', $data);
    }

    public function ambilDataSiswa()
    {
        // ambil variabel POST
        $kelas = $this->request->getVar('kelas');
        $idKelas = $this->request->getVar('id_kelas');
        $tanggal = $this->request->getVar('tanggal');

        // $lewat = Time::parse($tanggal)->isAfter(Time::today());

        // $result = $this->presensiSiswa->getPresensiByKelasTanggal($idKelas, $tanggal);

        $data = [
            'kelas' => $kelas,
            // 'data' => $result,
            // 'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
            // 'lewat' => $lewat
        ];

        return view('admin/penilaian/list-nilai-siswa', $data);
    }
}
