<?php

namespace App\Controllers;

use App\Libraries\AuthHelpers;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PengajuanJudulController extends ResourceController
{
    protected $modelName = 'App\Models\PengajuanJudulModel';
    protected $format = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'message' => 'Selamat datang di API Pengajuan Judul',
            'data_pengajuan_dosen' => $this->model->findAll()
        ];
        return $this->respond($data, 200);
    }

    public function insertJudul()
    {
        $data = $this->request->getJSON(true); // auto jadi array

        if (empty($data['id_pengajuan_dosen']) || empty($data['npm']) || empty($data['judul'])) {
            return $this->failValidationErrors('id_pengajuan_dosen, npm, dan judul wajib diisi.');
        }

        $insert = $this->model->insertPengajuanJudul($data);

        if (!$insert) {
            return $this->fail('Pengajuan judul gagal. Pastikan status disetujui dan npm sesuai.', 400);
        }

        return $this->respondCreated([
            'message' => 'Pengajuan judul berhasil disimpan.',
            'data' => $data
        ]);
    }

    public function ambilPengajuanUser(){

        $user = AuthHelpers::getUserFromToken($this->request);

        if ($user->role == 'dosen') {

            $id = $user->id_user;

            $dosen = new DosenModel();
            $nidn = $dosen->getNIDNbyID($id);

            $getpengajuan = $this->model->getbyNIDN($nidn);

            if (!$getpengajuan) {
                return $this->failNotFound('Pengajuan Judul kepada dosen dengan NIDN = ' . $nidn . " tidak ada!!");
            }

            // Kalau validasi berhasil, return balik
            return $this->respond([
                'message' => 'Data ditemukan',
                'data_pengajuan_dosen' => $getpengajuan
            ], 200);

        }else if ($user->role == "mahasiswa") {

            $id = $user->id_user;

            $mahasiswa = new MahasiswaModel();
            $npm = $mahasiswa->getNPMbyID($id);

            $getpengajuan = $this->model->getbyNPM($npm);

            if (!$getpengajuan) {
                return $this->failNotFound('Pengajuan Judul oleh Mahasiswa dengan NPM = ' . $npm . " tidak ada!!");
            }
    
            // Kalau validasi berhasil, return balik
            return $this->respond([
                'message' => 'Data ditemukan',
                'data_pengajuan_dosen' => $getpengajuan
            ], 200);
        }else{
            return $this->failUnauthorized("Pengguna tidak dikenali, akses ditolak");
        }
    }

    public function updateDecision($id)
    {

        $data = $this->request->getJSON();

        $status = $data->status ?? null;

        $saran = $data->saran ?? null;

        if (!$id || !$status || !$saran) {
            return $this->failValidationErrors('id, status, dan saran harus diisi.');
        }

        $user = AuthHelpers::getUserFromToken($this->request);

        $pengajuan = $this->model->find($id);
        $id_user = $user->id_user;

        $dosen = new DosenModel();
        $nidn = $dosen->getNIDNbyID($id_user);

        // Cek apakah pengajuan ini milik user yang login
        if (!$pengajuan) {
            return $this->failNotFound("Pengajuan tidak ada");
        } else if ($pengajuan['nidn'] !== $nidn) {
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan ini.");
        }


        $update = $this->model->giveDecision($id, $status, $saran);

        if (!$update) {
            return $this->fail(
                "Update gagal dilakukan",
                400
            );
        }

        // Ambil ulang datanya
        $data = $this->model->find($id);

        return $this->respond([
            'message' => 'Keputusan berhasil disimpan.',
            'data_pengajuan_judul' => $data
        ], 200);

    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $user = AuthHelpers::getUserFromToken($this->request);

        $pengajuan = $this->model->getIdentityByIDJudul($id);
        
        if (!$pengajuan) {
            return $this->failNotFound("Pengajuan judul tidak ditemukan.");
        }

        $id_user = $user->id_user;
        $role = $user->role;

        // Dapatkan identitas user
        $npm = (new MahasiswaModel())->getNPMbyID($id_user);
        $nidn = (new DosenModel())->getNIDNbyID($id_user);

        // Cek berdasarkan role
        if ($role === 'mahasiswa' && $pengajuan['npm'] !== $npm) {
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan judul ini.");
        }

        if ($role === 'dosen' && $pengajuan['nidn'] !== $nidn) {
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan judul ini.");
        }

        return $this->respond([
            'message' => 'Data pengajuan judul berhasil ditemukan.',
            'data_pengajuan_dosen' => $pengajuan
        ]);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        // 
    }


    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $user = AuthHelpers::getUserFromToken($this->request);
        $data = $this->request->getRawInput(true);
    
        // Cari data pengajuan berdasarkan ID
        $pengajuan = $this->model->find($id);

        $id_user = $user->id_user;

        $mahasiswa = new MahasiswaModel();
        $npm = $mahasiswa->getNPMbyID($id_user);
    
        // Cek apakah pengajuan ini milik user yang login
        if (!$pengajuan) {
            return $this->failNotFound("Pengajuan tidak ada");
        }else if ($pengajuan['npm'] !== $npm){
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan ini.");
        }
    
        // Update kalau cocok
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors(), 400);
        }
    
        return $this->respond([
            'message' => 'Update berhasil',
            'data' => $data
        ]);
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $user = AuthHelpers::getUserFromToken($this->request);

        // Cari data pengajuan berdasarkan ID
        $pengajuan = $this->model->find($id);

        $id_user = $user->id_user;

        $mahasiswa = new MahasiswaModel();
        $npm = $mahasiswa->getNPMbyID($id_user);

        // Cek apakah pengajuan ini milik user yang login
        if (!$pengajuan) {
            return $this->failNotFound("Pengajuan tidak ada");
        } else if ($pengajuan['npm'] !== $npm) {
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan ini.");
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => "Data pengajuan judul dengan ID ".$id." berhasil dihapus"
        ]);
    }
}
