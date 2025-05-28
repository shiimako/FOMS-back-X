<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';

    public function index()
    {
        // Cek dulu modelnya kebaca atau nggak
        if (!is_object($this->model)) {
            return $this->failServerError('Model not loaded properly.');
        }

        $data = [
            'message' => 'Selamat datang di API User',
            'data_user' => $this->model->findAll()
        ];
        return $this->respond($data, 200);

    }

    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        if (!$email || !$password) {
            return $this->failValidationErrors('Email dan password harus diisi.');
        }

        $user = $this->model->getUserByEmailAndPassword($email, $password);

        if (!$user) {
            return $this->failNotFound('Email atau password salah.');
        }

        // Kalau validasi berhasil, bisa kirim data user atau token dsb.
        return $this->respond([
            'message' => 'Login berhasil',
            'user' => $user
        ], 200);
    }

    public function add()
    {
        $data = $this->request->getRawInput(true);
        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }else{
            return $this->respondCreated([
                'message' => 'Data User berhasil ditambahkan',
                'data' => $data
            ]);
        }
    }

    public function update($id = null)
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $role = $this->request->getVar('role');

        if (!$this->model->find($id)) {
            return $this->failNotFound("User dengan ID ".$id." tidak ditemukan.");
        }

        if (!$email) {
            return $this->failValidationErrors('Email Kosong');
        }elseif (!$password) {
            return $this->failValidationErrors('Password Kosong');
        }elseif (!$role) {
            return $this->failValidationErrors('Role Kosong');
        }

        $data = [
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ];
        $update = $this->model->update($id, $data);

        if (!$update){
            return $this->fail('Data tidak dapat diupdate!!');
        }

        return $this->respond([
            'message' => 'Update berhasil',
            'user' => $data
        ], 200);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound("User dengan ID ".$id." tidak ditemukan.");
        }
        $delete = $this->model->delete($id);

        if (!$delete) {
            return $this->fail('Data tidak dapat dihapus!!');
        }

        return $this->respond([
            'message' => "id [".$id."] berhasil dihapus!!"
        ], 200);
    }

}
