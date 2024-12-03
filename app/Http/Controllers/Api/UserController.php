<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

use App\Helpers\User\UserHelper;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new UserHelper();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = [
            'nama' => $request->nama ?? '',
            'email' => $request->email ?? '',
        ];

        $sort = $request->sort ?? 'id DESC';

        if (!preg_match('/^[a-zA-Z0-9_]+ (ASC|DESC)$/', $sort)) {
            $sort = 'id DESC';  // Default sorting kalau yang diinput invalid
        }

        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 25;

        // Call getAll with the correct number of arguments
        $users = $this->user->getAll($filter, $perPage, $sort);

        return response()->success([
            'list' => UserResource::collection($users['data']),
            'meta' => [
                'total' => $users['total']
            ]
        ]);
    }

    /**
     * Membuat data user baru & disimpan ke tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function store(userRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/userRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'm_user_roles_id']);
        $user = $this->user->create($payload);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(new UserResource($user['data']), 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->user->getById($id);

        if (!$user['status'] || is_null('data')) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->success(new UserResource($user['data']));
    }

    /**
     * Mengubah data user di tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function update(userRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/userRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'id', 'photo', 'm_user_roles_id']);
        $user = $this->user->update($payload, $payload['id']);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(new UserResource($user['data']), 'User berhasil diubah');
    }

    /**
     * Soft delete data user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function destroy($id)
    {
        $user = $this->user->delete($id);

        if (!$user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($user);
    }
}
