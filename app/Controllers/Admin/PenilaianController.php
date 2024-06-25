<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\JurusanModel;
use App\Models\PenilaianModel;

class PenilaianController extends BaseController
{
    protected KelasModel $kelasModel;

    protected SiswaModel $siswaModel;

    protected PenilaianModel $penilaianModel;


    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->penilaianModel = new PenilaianModel();
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


        return view('admin/penilaian/penilaian-siswa', $data);
    }

    public function ambilDataSiswa()
    {
        // ambil variabel POST
        $kelas = $this->request->getVar('kelas');
        $idkelas = $this->request->getVar('id_kelas');
        $tahun = $this->request->getVar('tahun');
        $triwulan = $this->request->getVar('triwulan');

        // $lewat = Time::parse($tanggal)->isAfter(Time::today());
        // $lewat = Time::parse($tanggal)->isAfter(Time::today());

        $result = $this->penilaianModel->get_nilai_by_triwulan($idkelas, $tahun, $triwulan);

        $data = [
            'kelas' => $kelas,
            'data' => $result,
            // 'listnilai' => $this->penilaianModel->get_nilai(),
            // 'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
            // 'lewat' => $lewat
        ];
        dd($data);

        return view('admin/penilaian/list-nilai-siswa', $data);
    }

    public function ambil_nilai()
    {
        $idpenilaian = $this->request->getVar('id_penilaian');
        $idsiswa = $this->request->getVar('id_siswa');

        $data = [
            'penilaian' => $this->penilaianModel->get_nilai_by_id($idpenilaian),
            'data' => $this->siswaModel->getSiswaById($idsiswa)
        ];

        return view('admin/penilaian/ubah-nilai-modal', $data);
    }

    public function ubah_nilai()
    {
        // ambil variabel POST
        $idpenilaian = $this->request->getVar('id_penilaian');
        $idsiswa = $this->request->getVar('id_siswa');
        $idKelas = $this->request->getVar('id_kelas');
        $tahun = $this->request->getVar('tahun');
        $triwulan = $this->request->getVar('triwulan');
        $sosial = $this->request->getVar('sosial');
        $edukasi = $this->request->getVar('edukasi');
        $pisik = $this->request->getVar('pisik');
        $teknik = $this->request->getVar('teknik');
        $taktik = $this->request->getVar('taktik');
        $rules = $this->request->getVar('rules');


        $cek = $this->penilaianModel->ceknilai($idsiswa, $triwulan, $tahun);

        $result = $this->penilaianModel->update_nilai(
            $cek == false ? NULL : $cek,
            $idsiswa,
            // $idpenilaian,
            $idsiswa,
            $idKelas,
            $tahun,
            $triwulan,
            $sosial,
            $edukasi,
            $pisik,
            $teknik,
            $taktik,
            $rules
            // $idKelas,
            // $tanggal,
            // $idKehadiran,
            // $jamMasuk ?? NULL,
            // $jamKeluar ?? NULL,
            // $keterangan

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
