<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'tb_penilaian_siswa';
    protected $primaryKey       = 'id_penilaian';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siswa', 'id_kelas', 'tahun', 'triwulan', 'sosial', 'edukasi', 'phisical_motoric', 'teknik', 'taktik', 'rules'
    ];

    public function get_nilai()
    {
        return $this->findAll();
    }

    public function ceknilai(string|int $idsiswa, $tahun, string $triwulan)
    {
        $result = $this->where(['id_siswa' => $idsiswa, 'triwulan' => $triwulan, 'tahun' => $tahun])->first();

        if (empty($result)) return false;

        return $result[$this->primaryKey];
    }

    public function simpan_nilai(string $idsiswa, $idKelas, $tahun, $triwulan, $sosial, $edukasi, $pisik, $teknik, $taktik, $rules)
    {
        $this->save([
            'id_siswa' => $idsiswa,
            'id_kelas' => $idKelas,
            'tahun' => $tahun,
            'triwulan' => $triwulan,
            'sosial' => $sosial,
            'edukasi' => $edukasi,
            'phisical_motoric' => $pisik,
            'teknik' => $teknik,
            'taktik' => $taktik,
            'rules' => $rules,

        ]);
    }

    public function get_nilai_by_id(string $idpenilaian)
    {
        return $this->where([$this->primaryKey => $idpenilaian])->first();
    }


    public function get_nilai_by_idsiswa($idsiswa)
    {
        return $this->where(['id_siswa' => $idsiswa])->first();
    }

    public function get_nilai_by_kelas_tahun($idKelas, $tahun)
    {
        return $this->setTable('tb_siswa')
            ->select('*')
            ->join(
                "(SELECT id_penilaian, id_siswa AS id_siswa_penilaian, tahun, triwulan, sosial, edukasi, phisical_motoric,teknik,taktik,rules FROM tb_penilaian_siswa)tb_penilaian_siswa",
                "{$this->table}.id_siswa = tb_penilaian_siswa.id_siswa_penilaian AND id_siswa_penilaian.tahun = '$tahun'",
                'LEFT'
            )
            ->join(
                'tb_kehadiran',
                'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran',
                'LEFT'
            )
            ->where("{$this->table}.id_kelas = $idKelas")
            ->orderBy("id_siswa")
            ->findAll();
    }

    public function get_nilai_by_triwulan(string $triwulan, $tahun)
    {
        $this->join(
            'tb_siswa',
            "tb_penilaian_siswa.id_siswa = tb_siswa.id_siswa AND tb_penilaian_siswa.tahun = '$tahun'",
            'right'
        );
        if ($triwulan == '1') {
            $result = $this->findAll();

            $filteredResult = [];

            foreach ($result as $value) {
                if ($value['triwulan'] != ('1' || '2' || '3')) {
                    array_push($filteredResult, $value);
                }
            }

            return $filteredResult;
        } else {
            $this->where(['tb_penilaian_siswa.triwulan' => $triwulan]);
            return $this->findAll();
        }
    }

    public function update_nilai(
        $idpenilaian,
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

    ) {
        // $penilaian = $this->get_nilai_by_idsiswa($idsiswa);

        $data = [

            'id_siswa' => $idsiswa,
            'id_kelas' => $idKelas,
            'tahun' => $tahun,
            'triwulan' => $triwulan,
            'sosial' => $sosial,
            'edukasi' => $edukasi,
            'phisical_motoric' => $pisik,
            'teknik' => $teknik,
            'taktik' => $taktik,
            'rules' => $rules,
        ];

        if ($idpenilaian != null) {
            $data[$this->primaryKey] = $idpenilaian;
        }

        if ($tahun != null) {
            $data['tahun'] = $tahun;
        }

        if ($triwulan != null) {
            $data['triwulan'] = $triwulan;
        }

        return $this->save($data);
    }
}
