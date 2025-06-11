<?php

namespace App\Controllers;

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

    // Ngelihat Pengajuan Dosen by NIDN (pov Dosen)
    public function ambildariNIDN($nidn)
    {
        if (!$nidn) {
            return $this->failValidationErrors('NIDN harus diisi.');
        }

        $getnidn = $this->model->getbyNIDN($nidn);

        if (!$getnidn) {
            return $this->failNotFound('Dosen dengan NIDN = '.$nidn." tidak ada!!");
        }

        // Kalau validasi berhasil, return balik
        return $this->respond([
            'message' => 'Data ditemukan',
            'data_pengajuan_dosen' => $getnidn
        ], 200);
    }


    // Ngelihat Pengajuan Mahasiswa by NIDN (pov Mahasiswa)
    public function ambildariNPM($npm)
    {
        if (!$npm) {
            return $this->failValidationErrors('NPM harus diisi.');
        }

        $getnpm = $this->model->getbyNPM($npm);

        if (!$getnpm) {
            return $this->failNotFound('Mahasiswa dengan NPM = ' . $npm . " tidak ada!!");
        }

        // Kalau validasi berhasil, return balik
        return $this->respond([
            'message' => 'Data ditemukan',
            'data_pengajuan_dosen' => $getnpm
        ], 200);
    }

    public function updateDecision($id){

        $data = $this->request->getJSON();

        $status = $data->status ?? null;

        if (!$id || !$status) {
            return $this->failValidationErrors('id dan status harus diisi.');
        }

        $update = $this->model->giveDecision($id, $status);

        if (!$update)
        {
            return $this->fail(
            "Update gagal dilakukan",
            400);
        }

        // Ambil ulang datanya
        $data = $this->model->find($id);

        return $this->respond([
            'message' => 'Keputusan berhasil disimpan.',
            'data_pengajuan_dosen' => $data
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
        //
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
        $data = $this->request->getJSON(true); // Ambil data JSON dari body requestt

        if (!$this->model->insert($data)) {
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
        $data = $this->request->getRawInput(true) ?? $this->request->getPost();

        // Cek apakah data mahasiswa dengan $id ada
        if (!$this->model->find($id)) {
            return $this->failNotFound("Data pengajuan dosen dengan ID $id tidak ditemukan");
        }

        // Update data mahasiswa
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors(), 400);
        }

        // Respond dengan pesan sukses
        return $this->respond([
            'message' => 'Data pengajuan dosen berhasil diperbarui',
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
        if (!$this->model->find($id)) {
            return $this->failNotFound("Data pengajuan dosen dengan ID $id tidak ditemukan");
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => "Data pengajuan dosen dengan ID $id berhasil dihapus"
        ]);
    }
}
