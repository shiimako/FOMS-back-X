<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanDosenModel extends Model
{
    protected $table            = 'pengajuan_dosen';
    protected $primaryKey       = 'id_pengajuan_dosen';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pengajuan_dosen',
        'npm',
        'nidn',
        'tgl_pengajuan',
        'status',
    ];

    // Ngelihat Pengajuan Dosen by NIDN - Pending(pov Dosen)
    public function getbyNIDNPending($nidn)
    {
        return $this->db->table('pengajuan_dosen pd')
            ->select('pd.*')
            ->join('dosen d', 'pd.nidn = d.nidn')
            ->where('d.nidn', $nidn)
            ->where('pd.status', 'pending')
            ->get()
            ->getResult();
    }

    // Ngelihat Pengajuan Dosen by NIDN - Approved (pov Dosen)
    public function getbyNIDNApproved($nidn)
    {
        return $this->db->table('pengajuan_dosen pd')
            ->select('pd.*')
            ->join('dosen d', 'pd.nidn = d.nidn')
            ->where('d.nidn', $nidn)
            ->where('pd.status', 'approved')
            ->get()
            ->getResult();
    }

    // Ngelihat Pengajuan Dosen by NPM (pov Mahasiswa)
    public function getbyNPM($npm)
    {
        return $this->db->table('pengajuan_dosen pd')
            ->select('pd.*')
            ->join('mahasiswa m', 'pd.npm = m.npm')
            ->where('m.npm', $npm)
            ->get()
            ->getResult();
    }

    public function caribyNamaMhs($nama, $nidn)
    {
        return $this->db->table('pengajuan_dosen pd')
             ->select('pd.*, d.nama_dosen, m.nama_mhs')
            ->join('mahasiswa m', 'pd.npm = m.npm')
            ->join('dosen d', 'pd.nidn = d.nidn')
            ->like('m.nama_mhs', $nama)
            ->where('d.nidn', $nidn);
    }

    public function caribyNamaDsn($nama, $npm)
    {
        return $this->db->table('pengajuan_dosen pd')
            ->select('pd.*, d.nama_dosen, m.nama_mhs')
            ->join('mahasiswa m', 'pd.npm = m.npm')
            ->join('dosen d', 'pd.nidn = d.nidn')
            ->like('d.nama_dosen', $nama)
            ->where('m.npm', $npm)
            ->get()
            ->getRowArray();
    }

    // Ngelihat Pengajuan Dosen by NPM (pov Mahasiswa)
    public function getDosenPembimbing($npm)
    {
        return $this->db->table('pengajuan_dosen pd')
            ->select('d.nidn, d.nama_dosen')
            ->join('dosen d', 'pd.nidn = d.nidn')
            ->join('mahasiswa m', 'pd.npm = m.npm')
            ->where('m.npm', $npm)
            ->where('pd.status', 'approved')
            ->get()
            ->getResult();
    }

    // Edit Pengajuan Dosen oleh Dosen (Setuju atau Tolak)
    public function giveDecision($id, $status)
    {
        if (!in_array($status, ['approved', 'rejected'])) {
            return false; // buat jaga-jaga status aneh
        }

        return $this->db->table('pengajuan_dosen')
            ->where('	id_pengajuan_dosen ', $id)
            ->update([
                'status' => $status
            ]);
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
    protected $validationRules      = [];
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
