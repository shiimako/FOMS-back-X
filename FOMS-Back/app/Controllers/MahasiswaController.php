<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class MahasiswaController extends ResourceController
{
    protected $modelName = 'App\Models\MahasiswaModel';
    protected $format = 'json';

    public function index()
    {
        $data = [
            'message' => 'Selamat datang di API Mahasiswa',
            'data_mahasiswa' => $this->model->findAll()
        ];
        return $this->respond($data, 200);
    }

    public function show($id = null)
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
        $data = $this->request->getJSON(true); // Ambil data JSON dari body request

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        } else {
            return $this->respondCreated([
                'message' => 'Data Mahasiswa berhasil ditambahkan',
                'data' => $data
            ]);
        }
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
        // Ambil data dari body request
        $data = $this->request->getJSON(true);

        // Cek apakah data mahasiswa dengan $id ada
        if (!$this->model->find($id)) {
            return $this->failNotFound("Mahasiswa dengan ID $id tidak ditemukan");
        }

        // Update data mahasiswa
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors(), 400);
        }

        // Respond dengan pesan sukses
        return $this->respond([
            'message' => 'Data mahasiswa berhasil diperbarui',
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
            return $this->failNotFound("Mahasiswa dengan ID $id tidak ditemukan");
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'message' => "Data mahasiswa dengan ID $id berhasil dihapus"
        ]);
    }
}
