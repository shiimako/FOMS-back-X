<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'email',
        'password',
        'role',
        'created_at',
        'updated_at'
    ];

    public function getUserByEmailAndPassword(string $email, string $password)
    {
        return $this->where('email', $email)
            ->where('password', $password)
            ->first();
    }

    public function getDataDosenLengkapByID($id_user)
    {
        return $this->db->table('users u')
            ->select('u.id_user, u.email, u.password, d.nidn, d.nama_dosen, d.telp_dosen')
            ->join('dosen d', 'u.id_user = d.id_user')
            ->where('u.id_user', $id_user)
            ->get()
            ->getRowArray(); // hasilnya berupa object
    }

    public function getDataMahasiswaLengkapByID($id_user)
    {
        return $this->db->table('users u')
            ->select('u.id_user, u.email, u.password, m.npm, m.nama_mhs, m.kls_mhs, m.jurusan_mhs, m.prodi_mhs, m.telp_mhs')
            ->join('mahasiswa m', 'u.id_user = m.id_user')
            ->where('u.id_user', $id_user)
            ->get()
            ->getRowArray(); // hasilnya berupa object juga
    }

    public function getDataAdminLengkapByID($id_user)
    {
        return $this->select('id_user, email, password')
            ->where('id_user', $id_user)
            ->get()
            ->getRowArray(); // hasilnya berupa object juga
    }



    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_user' => 'required',
        'email' => 'required|valid_email',
         'password' => 'required|min_length[6]',
        'role' => 'required'
    ];
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
