<?php

namespace App\Controllers;

use App\Libraries\AuthHelpers;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PengajuanDosenController extends ResourceController
{
    protected $modelName = 'App\Models\PengajuanDosenModel';
    protected $format = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'message' => 'Selamat datang di API Pengajuan Dosen',
            'data_pengajuan_dosen' => $this->model->findAll()
        ];
        return $this->respond($data, 200);
    }

    public function ambilPengajuanUser()
    {

        $user = AuthHelpers::getUserFromToken($this->request);

        if ($user->role == 'dosen') {

            $id = $user->id_user;

            $dosen = new DosenModel();
            $nidn = $dosen->getNIDNbyID($id);


            if (!$nidn) {
                return $this->failNotFound("NIDN tidak ditemukan untuk user ini.");
            }

            $getpengajuan = $this->model->getbyNIDNPending($nidn);

            if (!$getpengajuan) {
                return $this->failNotFound('Pengajuan Dosen kepada dosen dengan NIDN = ' . $nidn . " tidak ada!!");
            }

            foreach ($getpengajuan as &$pengajuan) {
                $npm = $pengajuan['npm'];
                $mahasiswa = (new MahasiswaModel())->find($npm);
                $pengajuan['nama_mhs'] = $mahasiswa['nama_mhs'] ?? '-';
            }

            // Kalau validasi berhasil, return balik
            return $this->respond([
                'message' => 'Data ditemukan',
                'data_pengajuan_dosen' => $getpengajuan
            ], 200);
        } else if ($user->role == "mahasiswa") {

            $id = $user->id_user;

            $mahasiswa = new MahasiswaModel();
            $npm = $mahasiswa->getNPMbyID($id);

            $getpengajuan = $this->model->getbyNPM($npm);

            if (!$getpengajuan) {
                return $this->failNotFound('Pengajuan Dosen oleh Mahasiswa dengan NPM = ' . $npm . " tidak ada!!");
            }

            // Tambahkan nama_dosen ke setiap item
            foreach ($getpengajuan as &$pengajuan) {
                $nidn = $pengajuan['nidn'];
                $dosen = (new DosenModel())->find($nidn);
                $pengajuan['nama_dosen'] = $dosen['nama_dosen'] ?? '-';
            }

            // Kalau validasi berhasil, return balik
            return $this->respond([
                'message' => 'Data ditemukan',
                'data_pengajuan_dosen' => $getpengajuan
            ], 200);
        } else {
            return $this->failUnauthorized("Pengguna tidak dikenali, akses ditolak");
        }
    }

    public function ambilBimbingan()
    {
        $user = AuthHelpers::getUserFromToken($this->request);

        if ($user->role == 'dosen') {

            $id = $user->id_user;

            $dosen = new DosenModel();
            $nidn = $dosen->getNIDNbyID($id);


            if (!$nidn) {
                return $this->failNotFound("NIDN tidak ditemukan untuk user ini.");
            }

            $getpengajuan = $this->model->getbyNIDNApproved($nidn);
            foreach ($getpengajuan as &$pengajuan) {
                $npm = $pengajuan['npm'];
                $mahasiswa = (new MahasiswaModel())->find($npm);
                $pengajuan['nama_mhs'] = $mahasiswa['nama_mhs'] ?? '-';
            }

            if (!$getpengajuan) {
                return $this->failNotFound('Mahasiswa bimbingan kepada dosen dengan NIDN = ' . $nidn . " tidak ada!!");
            }

            // Kalau validasi berhasil, return balik
            return $this->respond([
                'message' => 'Data ditemukan',
                'data_bimbingan' => $getpengajuan
            ], 200);
        }

        return $this->failUnauthorized("Pengguna tidak dikenali, akses ditolak");
    }

    public function updateDecision($id)
    {

        $data = $this->request->getJSON();

        $status = $data->status ?? null;

        if (!$id || !$status) {
            return $this->failValidationErrors('id dan status harus diisi.');
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

        $update = $this->model->giveDecision($id, $status);

        if (!$update) {
            if (!$update) {
                return $this->fail(
                    $this->model->errors() ?: 'Update gagal dilakukan',
                    400
                );
            }
        }

        // Ambil ulang datanya
        $data = $this->model->find($id);

        return $this->respond([
            'message' => 'Keputusan berhasil disimpan.',
            'data_pengajuan_dosen' => $data
        ], 200);
    }

    public function ambilPembimbing()
    {
        $user = AuthHelpers::getUserFromToken($this->request);

        if ($user->role == 'mahasiswa') {
            $id = $user->id_user;

            $mahasiswa = new MahasiswaModel();
            $npm = $mahasiswa->getNPMbyID($id);

            $getpengajuan = $this->model->getDosenPembimbing($npm);

            if (!$getpengajuan) {
                return null;
            }

            // Kalau validasi berhasil, return balik
            return $this->respond([
                'message' => 'Data ditemukan',
                'dosen' => $getpengajuan
            ], 200);
        } else {
            return $this->failUnauthorized("Pengguna tidak dikenali, akses ditolak");
        }
    }

    public function ambilbyNama($nama)
    {
        $user = AuthHelpers::getUserFromToken($this->request);
        $id_user = $user->id_user;
        $role = $user->role;

        $pengajuan = null;

        // Mahasiswa: cari dosen
        if ($role === 'mahasiswa') {
            $npm = (new MahasiswaModel())->getNPMbyID($id_user);
            $pengajuan = $this->model->caribyNamaDsn($nama, $npm);

            if (!$pengajuan) {
                return $this->failNotFound("Pengajuan dosen tidak ditemukan.");
            }
        }

        // Dosen: cari mahasiswa
        elseif ($role === 'dosen') {
            $nidn = (new DosenModel())->getNIDNbyID($id_user);
            $pengajuan = $this->model->caribyNamaMhs($nama, $nidn);

            if (!$pengajuan) {
                return $this->failNotFound("Pengajuan dosen tidak ditemukan.");
            }
        }

        // Role lain tidak boleh akses
        else {
            return $this->failForbidden("Role kamu tidak diizinkan mengakses data ini.");
        }

        return $this->respond([
            'message' => 'Data pengajuan dosen berhasil ditemukan.',
            'data_pengajuan_dosen' => $pengajuan
        ]);
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

        $pengajuan = $this->model->find($id);
        if (!$pengajuan) {
            return $this->failNotFound("Pengajuan dosen tidak ditemukan.");
        }

        $id_user = $user->id_user;
        $role = $user->role;

        // Dapatkan identitas user
        $npm = (new MahasiswaModel())->getNPMbyID($id_user);
        $nidn = (new DosenModel())->getNIDNbyID($id_user);

        // Cek berdasarkan role
        if ($role === 'mahasiswa' && $pengajuan['npm'] !== $npm) {
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan dosen ini.");
        }

        if ($role === 'dosen' && $pengajuan['nidn'] !== $nidn) {
            return $this->failForbidden("Kamu tidak punya akses ke pengajuan dosen ini.");
        }

        return $this->respond([
            'message' => 'Data pengajuan dosen berhasil ditemukan.',
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
        $user = AuthHelpers::getUserFromToken($this->request);
        $id = $user->id_user;
        $npm = (new MahasiswaModel())->getNPMbyID($id);
        $data = $this->request->getJSON(true); // Ambil data JSON dari body requestt
        $data['npm'] = trim(strval($npm));
        $data['id_pengajuan_dosen'] = generateIdPengajuanDosen();
        $tambah = $this->model->insert($data);

        if (!$tambah) {
            return $this->fail($this->model->errors(), 400);
        }

        return $this->respondCreated([
            'message' => 'Data pengajuan dosen berhasil ditambahkan',
            'data' => $data
        ]);
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
        } else if ($pengajuan['npm'] !== $npm) {
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
        } else if ($pengajuan['status'] !== 'pending') {
            return $this->failForbidden("Pengajuan hanya boleh dihapus apabila statusnya pending.");
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => "Data pengajuan dosen dengan ID $id berhasil dihapus"
        ]);
    }
}
