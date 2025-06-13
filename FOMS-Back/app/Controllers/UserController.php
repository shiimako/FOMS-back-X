<?php

namespace App\Controllers;

use App\Models\RefreshTokenModel;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\JWTService;
use Firebase\JWT\JWT;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $refreshTokenModel;

    public function __construct()
    {
        $this->refreshTokenModel = new RefreshTokenModel();
    }

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

        $jwt = new JWTService();
        $access_token = $jwt->generateToken([
            'id_user' => $user['id_user'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        $refresh_token = bin2hex(random_bytes(64));

        $this->refreshTokenModel->insert(
            [
                'id_user' => $user['id_user'],
                'token' => $refresh_token,
                'expired_at' => date('Y-m-d H:i:s', time() + (86400 * 7))
            ]
            );

        // Kalau validasi berhasil, bisa kirim data user atau token dsb.
        return $this->respond([
            'message' => 'Login berhasil',
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'user' => $user
        ], 200);
    }

    public function refresh()
    {
        $refreshToken = $this->request->getVar('refresh_token');

        // Cek token di database (misalnya pakai model)
        $record = $this->refreshTokenModel->where('token', $refreshToken)->first();

        if (!$record || strtotime($record['expired_at']) < time()) {
            return $this->failUnauthorized('Refresh token tidak valid atau sudah kedaluwarsa.');
        }

        $user = $this->refreshTokenModel->getUser($refreshToken);

        $jwt = new JWTService();
        $new_access_token = $jwt->generateToken([
            'id_user' => $user['id_user'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        return $this->respond([
            'new_access_token' => $new_access_token,
        ]);
    }

    public function logout()
    {
        $refreshToken = $this->request->getVar('refresh_token');

        if (!$refreshToken) {
            return $this->failValidationErrors('Refresh token harus disertakan.');
        }

        $tokenData = $this->refreshTokenModel->gettoken($refreshToken);

        if (!$tokenData) {
            return $this->failNotFound('Token tidak ditemukan.');
        }

        $hapusrefresh = $this->refreshTokenModel->hapustoken($refreshToken);

        if (!$hapusrefresh){
            return $this->fail("Gagal menghapus");
        }

        return $this->respond([
            'message' => 'Logout berhasil. Token dihapus.'
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
