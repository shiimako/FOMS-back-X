<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table            = 'mahasiswa';
    protected $primaryKey       = 'npm';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
                                    'npm',
                                    'id_user',
                                    'nama_mhs',
                                    'kls_mhs',
                                    'jurusan_mhs',
                                    'prodi_mhs',
                                    'telp_mhs',
                                    'created_at',
                                    'updated_at',
                                ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'npm' => 'required',
        'id_user' => 'required',
        'nama_mhs' => 'required',
        'kls_mhs' => 'required',
        'jurusan_mhs' => 'required',
        'prodi_mhs' => 'required',
        'telp_mhs' => 'required',
    ];

    public function getNPMbyID($id){
        return $this->where('id_user', $id)->first()['npm'] ?? null;
    }

    public function updateByUserID($id_user, $data)
    {
        return $this->where('id_user', $id_user)->set($data)->update();
    }

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
