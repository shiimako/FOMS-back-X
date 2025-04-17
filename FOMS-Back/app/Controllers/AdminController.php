<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Entities\User;
use App\Models\MahasiswaModel;
use App\Models\DosenModel;

class AdminController extends ResourceController
{
    protected $format = 'json';
    protected $userModel;
    protected $mahasiswaModel;
    protected $dosenModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->mahasiswaModel = new MahasiswaModel();
        $this->dosenModel = new DosenModel();
    }

    public function index()
    {
        return $this->respond([
            'message' => 'Selamat datang di API User',
            'data_mahasiswa' => $this->userModel->findAll()
        ]);
    }

    public function indexMahasiswa()
    {
        return $this->respond([
            'message' => 'Selamat datang di API Mahasiswa',
            'data_mahasiswa' => $this->mahasiswaModel->findAll()
        ]);
    }

    public function indexDosen()
    {
        return $this->respond([
            'message' => 'Selamat datang di API Dosen',
            'data_dosen' => $this->dosenModel->findAll()
        ]);
    }

    public function show($id = null)
    {
        $data = $this->userModel->find($id);
        if (!$data) return $this->failNotFound('User tidak ditemukan');

        return $this->respond($data);
    }

    public function showMahasiswa($id = null)
    {
        $data = $this->mahasiswaModel->find($id);
        if (!$data) return $this->failNotFound('Mahasiswa tidak ditemukan');

        return $this->respond($data);
    }

    public function showDosen($id = null)
    {
        $data = $this->dosenModel->find($id);
        if (!$data) return $this->failNotFound('Dosen tidak ditemukan');

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getRawInput(true); // Ambil data JSON dari body request

        if (!$this->userModel->insert($data)) {
            return $this->fail($this->userModel->errors(), 400);
        }

        return $this->respondCreated([
            'message' => 'Data user berhasil ditambahkan',
            'data' => $data
        ]);
    }

    public function createMahasiswa()
    {
        $data = $this->request->getRawInput(true); // Ambil data JSON dari body request

        if (!$this->mahasiswaModel->insert($data)) {
            return $this->fail($this->mahasiswaModel->errors(), 400);
        }

        return $this->respondCreated([
            'message' => 'Data Mahasiswa berhasil ditambahkan',
            'data' => $data
        ]);
    }

    public function createDosen(){
        $data = $this->request->getRawInput(true); // Ambil data JSON dari body request
        if (!$this->dosenModel->insert($data)) {
            return $this->fail($this->dosenModel->errors(), 400);
            }

        return $this->respondCreated([
            'message' => 'Data Dosen berhasil ditambahkan',
            'data' => $data
            ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        if (!$this->userModel->update($id, $data)) {
            return $this->failValidationErrors($this->userModel->errors());
        }

        return $this->respond(['message' => 'User updated']);
    }

    public function updateMahasiswa($id = null)
    {
        $data = $this->request->getJSON(true);
        if (!$this->mahasiswaModel->update($id, $data)) {
            return $this->failValidationErrors($this->mahasiswaModel->errors());
        }

        return $this->respond(['message' => 'Mahasiswa updated']);
    }

    public function updateDosen($id = null)
    {
        $data = $this->request->getJSON(true);
        if (!$this->dosenModel->update($id, $data)) {
            return $this->failValidationErrors($this->dosenModel->errors());
        }

        return $this->respond(['message' => 'Dosen updated']);
    }

    public function delete($id = null)
    {
        if (!$this->userModel->delete($id)) {
            return $this->failNotFound('User not found');
        }

        return $this->respondDeleted(['message' => 'User deleted']);
    }

    public function deleteMahasiswa($id = null)
    {
        if (!$this->mahasiswaModel->delete($id)) {
            return $this->failNotFound('Mahasiswa not found');
        }

        return $this->respondDeleted(['message' => 'Mahasiswa deleted']);
    }

    public function deleteDosen($id = null)
    {
        if (!$this->dosenModel->delete($id)) {
            return $this->failNotFound('Dosen not found');
        }

        return $this->respondDeleted(['message' => 'Dosen deleted']);
    }
}
