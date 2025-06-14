<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanJudulModel extends Model
{
    protected $table            = 'pengajuan_judul';
    protected $primaryKey       = 'id_pengajuan_judul';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pengajuan_judul',
        'id_pengajuan_dosen',
        'judul',
        'saran',
        'tgl_pengajuan',
        'status',
        'proposal_file',
    ];
    public function getbyNIDN($nidn)
    {
        return $this->db->table('pengajuan_judul pj')
            ->select('pj.*')
            ->join('pengajuan_dosen pd', 'pj.id_pengajuan_dosen = pd.id_pengajuan_dosen')
            ->where('pd.nidn', $nidn)
            ->get()
            ->getResult();
    }

    public function getbyNPM($npm)
    {
        return $this->db->table('pengajuan_judul pj')
            ->select('pj.*')
            ->join('pengajuan_dosen pd', 'pj.id_pengajuan_dosen = pd.id_pengajuan_dosen')
            ->where('pd.npm', $npm)
            ->get()
            ->getResult();
    }

    public function getIdentityByIDJudul($id_pengajuan_judul)
    {
        return $this->db->table('pengajuan_judul pj')
            ->select('pj.id_pengajuan_judul, pd.id_pengajuan_dosen, m.npm, d.nidn')
            ->join('pengajuan_dosen pd', 'pj.id_pengajuan_dosen = pd.id_pengajuan_dosen')
            ->join('mahasiswa m', 'm.npm = pd.npm')
            ->join('dosen d', 'd.nidn = pd.nidn')
            ->where('pj.id_pengajuan_judul', $id_pengajuan_judul)
            ->get()
            ->getRowArray(); // kalau kamu mau ambil 1 baris aja
    }


    public function insertPengajuanJudul($data)
    {
        $idPengajuan = $data['id_pengajuan_dosen'] ?? null;
        $judul = $data['judul'] ?? null;
        $npm = $data['npm'] ?? null;

        if (!$idPengajuan || !$judul || !$npm) {
            return false;
        }

        // Ambil pengajuan_dosen-nya
        $pengajuan = $this->db->table('pengajuan_dosen')
            ->where('id_pengajuan_dosen', $idPengajuan)
            ->get()->getRow();

        // Cek: harus ada, status = approved, dan npm-nya sama
        if (!$pengajuan || $pengajuan->status !== 'approved' || $pengajuan->npm !== $npm) {
            return false;
        }

        // Hapus data npm sebelum insert (nggak dibutuhin di tabel pengajuan_judul)
        unset($data['npm']);

        // Insert ke pengajuan_judul
        return $this->db->table('pengajuan_judul')->insert($data);
    }


    public function giveDecision($id, $status, $saran)
    {
        if (!in_array($status, ['approved', 'rejected'])) {
            return false; // buat jaga-jaga status aneh
        }

        return $this->db->table('pengajuan_judul')
            ->where('id_pengajuan_judul', $id)
            ->update([
                'status' => $status,
                'saran' => $saran
            ]);
    }



    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_pengajuan_judul' => 'required',
        'id_pengajuan_dosen' => 'required',
        'judul' => 'required',
        'proposal_file' => 'required',
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
