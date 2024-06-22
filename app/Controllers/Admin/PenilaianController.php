<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\JurusanModel;

class PenilaianController extends BaseController
{
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected JurusanModel $jurusanModel;
    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
    }
    public function index()
    {
        // Menampilkan daftar Penilaian SIswa
        $kelas = $this->kelasModel->getAllKelas();

        $data = [
            'title' => 'Data Nilai Siswa',
            'ctx' => 'nilai-siswa',
            'kelas' => $kelas
        ];


        return view('admin/penilaian/penilaian', $data);
    }

    public function ambilDataSiswa()
    {
    }
}
