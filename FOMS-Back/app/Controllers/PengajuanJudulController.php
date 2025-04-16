<?php

namespace App\Controllers;

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
            'message' => 'Selamat datang di API Dosen',
            'data_pengajuan_dosen' => $this->model->findAll()
        ];
        return $this->respond($data, 200);
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
        $data = $this->request->getRawInput(true); // Ambil data JSON dari body request

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors(), 400);
        }

        return $this->respondCreated([
            'message' => 'Data pengajuan judul berhasil ditambahkan',
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
            return $this->failNotFound("Data pengajuan judul dengan ID $id tidak ditemukan");
        }

        // Update data mahasiswa
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors(), 400);
        }

        // Respond dengan pesan sukses
        return $this->respond([
            'message' => 'Data pengajuan judul berhasil diperbarui',
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
            return $this->failNotFound("Data pengajuan judul dengan ID $id tidak ditemukan");
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => "Data pengajuan judul dengan ID $id berhasil dihapus"
        ]);
    }
}
