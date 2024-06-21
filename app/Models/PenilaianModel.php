<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'tbl_penilaian';
    protected $primaryKey       = 'id_penilaian';

    public function getAllNilai()
    {
        return $this->findAll();
    }
}
