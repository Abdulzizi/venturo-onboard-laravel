<?php

namespace App\Helpers\User;

use App\Helpers\Venturo;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserHelper extends Venturo
{
    const USER_PHOTO_DIRECTORY = 'foto-user';
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = ''): array
    {
        $users = $this->userModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $users,
            'total' => $users->total(),
        ];
    }

    /**
     * Mengambil 1 data user dari tabel m_user
     *
     * @param integer $id id dari tabel m_user
     *
     * @return array
     */
    public function getById(string $id): array
    {
        $user = $this->userModel->getById($id);

        if (empty($user)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $user
        ];
    }

    /**
     * method untuk menginput data baru ke tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *  $payload['nama'] = string
     *  $payload['email] = string
     *  $payload['password] = string
     *
     * @return array
     */
    public function create(array $payload): array
    {
        try {
            $payload['password'] = Hash::make($payload['password']);

            $payload = $this->uploadGetPayload($payload);
            // dd($payload);
            $user = $this->userModel->store($payload);

            return [
                'status' => true,
                'data' => $user
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * method untuk mengubah user pada tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *  $payload['nama'] = string
     *  $payload['email] = string
     *  $payload['password] = string
     *
     * @return array
     */
    public function update(array $payload, string $id): array
    {
        try {
            if (isset($payload['password']) && !empty($payload['password'])) {
                $payload['password'] = Hash::make($payload['password']) ?: '';
            } else {
                unset($payload['password']);
            }

            // if (isset($payload['photo'])) {
            //     $payload['photo'] = $this->uploadGetPayload($payload);
            // }

            $payload = $this->uploadGetPayload($payload);

            // dd($payload);
            $this->userModel->edit($payload, $id);

            $user = $this->getById($id);
            return [
                'status' => true,
                'data' => $user['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Menghapus data user dengan sistem "Soft Delete"
     * yaitu mengisi kolom deleted_at agar data tsb tidak
     * keselect waktu menggunakan Query
     *
     * @param  integer $id id dari tabel m_user
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        try {
            $this->userModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Upload file and remove payload when photo is not exist
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     * @return array
     */
    private function uploadGetPayload(array $payload)
    {
        if (!empty($payload['photo'])) {
            if ($payload['photo'] instanceof \Illuminate\Http\UploadedFile) {
                // Handle file upload
                $fileName = $this->generateFileName($payload['photo'], 'USER_' . date('Ymdhis'));
                $photoPath = $payload['photo']->storeAs(self::USER_PHOTO_DIRECTORY, $fileName, 'public');
                $payload['photo'] = $photoPath;
            } else {
                // photo yang dikirim Base64-encoded, decode dan save
                $base64Image = $payload['photo'];
                list($type, $base64Image) = explode(';', $base64Image);
                list(, $base64Image) = explode(',', $base64Image);
                $decodedImage = base64_decode($base64Image);

                $fileName = 'USER_' . date('Ymdhis') . '.png';
                $photoPath = self::USER_PHOTO_DIRECTORY . '/' . $fileName;

                file_put_contents(storage_path('app/public/' . $photoPath), $decodedImage);
                $payload['photo'] = $photoPath;
            }
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }
}
