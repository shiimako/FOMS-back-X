<?php

namespace App\Controllers;

use App\Models\RefreshTokenModel;
use App\Libraries\AuthHelpers;
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

    public function show($id = null){

            $data = $this->model->find($id);
    
            if (!$data) {
                return $this->failNotFound("Data dengan ID $id tidak ditemukan.");
            }
    
            return $this->respond([
                'message' => 'Data berhasil ditemukan.',
                'data' => $data
            ]);
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

    public function userprofile(){
        $user = AuthHelpers::getUserFromToken($this->request);

        $id_user = $user->id_user;
        $role = $user->role;

                // Cek berdasarkan role
                if ($role === 'mahasiswa') {
                    $profile = $this->model->getDataMahasiswaLengkapByID($id_user);
                    if (!$profile){
                        return $this->failNotFound("Profil Mahasiswa tidak ditemukan");
                    }else if ($profile['id_user'] !== $id_user){
                        return $this->failForbidden("Anda tidak memiliki hak akses pada profil mahasiswa ini!");
                    }
                    return $this->respond([
                        'message' => 'Data Profil Mahasiswa berhasil ditemukan.',
                        'profil' => $profile
                        ]);
                }
                if ($role === 'dosen') {
                    $profile = $this->model->getDataDosenLengkapByID($id_user);
                    if (!$profile){
                        return $this->failNotFound("Profil Dosen tidak ditemukan");
                    }else if ($profile['id_user'] !== $id_user){
                        return $this->failForbidden("Anda tidak memiliki hak akses pada profil dosen ini!");
                    }
                    return $this->respond([
                        'message' => 'Data Profil Dosen berhasil ditemukan.',
                        'profil' => $profile
                        ]);
                }if ($role === 'admin') {
                    $profile = $this->model->getDataAdminLengkapByID($id_user);
                    if (!$profile) {
                    return $this->failNotFound("Profil Admin tidak ditemukan");
                    } else if ($profile['id_user'] !== $id_user) {
                    return $this->failForbidden("Anda tidak memiliki hak akses pada profil admin ini!");
                    }
                    return $this->respond([
                        'message' => 'Data Profil Admin berhasil ditemukan.',
                        'profil' => $profile
                    ]);
                }

                return $this->failForbidden("Tidak Punya Akses!!");
     }

    public function updateProfile()
    {
        $user = AuthHelpers::getUserFromToken($this->request);
        $id_user = $user->id_user;
        $role = $user->role;

        $input = $this->request->getJSON(true);

        // Data untuk tabel user (kalau ada)
        $userData = [];
        if (isset($input['email']))
            $userData['email'] = $input['email'];
        if (isset($input['password']))
            $userData['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

        // Ambil model user utama
        $userModel = new \App\Models\UserModel();

        // Update tabel user kalau ada data
        if (!empty($userData)) {
            if (!$userModel->update($id_user, $userData)) {
                return $this->fail($userModel->errors());
            }
        }

        // Update data spesifik berdasarkan role
        if ($role === 'mahasiswa') {
            $mahasiswaModel = new \App\Models\MahasiswaModel();

            $mhsData = array_intersect_key($input, array_flip([
                'nama_mhs',
                'kls_mhs',
                'jurusan_mhs',
                'prodi_mhs',
                'telp_mhs'
            ]));

            if (!$mahasiswaModel->updateByUserID($id_user, $mhsData)) {
                return $this->fail($mahasiswaModel->errors());
            }

            return $this->respond([
                'message' => 'Profil Mahasiswa berhasil diperbarui.',
                'updated' => ['user' => $userData, 'mahasiswa' => $mhsData]
            ]);

        } elseif ($role === 'dosen') {
            $dosenModel = new \App\Models\DosenModel();

            $dsnData = array_intersect_key($input, array_flip([
                'nama_dosen',
                'telp_dosen'
            ]));

            if (!$dosenModel->updateByUserID($id_user, $dsnData)) {
                return $this->fail($dosenModel->errors());
            }

            return $this->respond([
                'message' => 'Profil Dosen berhasil diperbarui.',
                'updated' => ['user' => $userData, 'dosen' => $dsnData]
            ]);

        } elseif ($role === 'admin') {
            // Admin hanya update dari tabel user
            return $this->respond([
                'message' => 'Profil Admin berhasil diperbarui.',
                'updated' => ['user' => $userData]
            ]);
        }

        return $this->failForbidden("Tidak punya akses.");
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
