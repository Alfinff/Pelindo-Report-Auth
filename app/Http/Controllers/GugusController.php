<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gugus;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GugusController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        try {
            $decodeToken = parseJwt($this->request->header('Authorization'));
            $role = $decodeToken->user->role;
            if ($role == "MHS") {
                $gugus = Gugus::where('uuid', $decodeToken->user->mahasiswa->gugus_id)->with('pemandu.user')->withCount(['mahasiswa'])->orderBy('created_at', 'desc');
            } elseif ($role == "SPA") {
                $gugus = Gugus::with('pemandu')->withCount(['mahasiswa'])->orderBy('created_at', 'desc');
            } elseif ($role == "PMD") {
                $gugus = Gugus::with('pemandu')->withCount(['mahasiswa'])->orderBy('created_at', 'desc');
            }


            if ($this->request->name) {
                $gugus = $gugus->where('name', 'like', '%' . $this->request->name . '%');
            }

            $gugus = $gugus->paginate(10);
            $gugus->setPath('https://gmedia.primakom.co.id/gmedia/superadmin/gugus');

            return response()->json([
                'success' => true,
                'message' => 'List gugus',
                'code'    => 200,
                'data'    => $gugus,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }

    public function indexNonPaginate()
    {
        try {
            $decodeToken = parseJwt($this->request->header('Authorization'));
            $role = $decodeToken->user->role;
            if ($role == "MHS") {
                $gugus = Gugus::where('uuid', $decodeToken->user->mahasiswa->gugus_id)->with('pemandu.user')->withCount(['mahasiswa'])->orderBy('created_at', 'desc');
            } elseif ($role == "SPA") {
                $gugus = Gugus::with('pemandu')->withCount(['mahasiswa'])->orderBy('created_at', 'desc');
            } elseif ($role == "PMD") {
                $gugus = Gugus::with('pemandu')->withCount(['mahasiswa'])->orderBy('created_at', 'desc');
            }


            if ($this->request->name) {
                $gugus = $gugus->where('name', 'like', '%' . $this->request->name . '%');
            }

            $gugus = $gugus->get();

            return response()->json([
                'success' => true,
                'message' => 'List gugus',
                'code'    => 200,
                'data'    => $gugus,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }

    public function store()
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|unique:gugus'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nama gugus telah digunakan',
                'code'    => 200,
            ]);
        }

        try {
            DB::beginTransaction();
            Gugus::create([
                'uuid' => generateUuid(),
                'name' => $this->request->name,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil buat gugus',
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());
        }
    }

    public function update($id)
    {
        $this->validate($this->request, [
            'name' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $gugus = Gugus::where('uuid', $id)->first();

            if (!$gugus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gugus tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $gugus->update([
                'name' => $this->request->name,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil ubah gugus',
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $gugus = Gugus::with('pemandu')
                ->where('uuid', $id)
                ->first();

            if (!$gugus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gugus tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data gugus',
                'code'    => 200,
                'data'    => $gugus,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $gugus = Gugus::where('uuid', $id)->first();

            if (!$gugus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gugus tidak ditemukan',
                    'code'    => 404,
                ]);
            }

            $gugus->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil hapus gugus',
                'code'    => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return writeLog($th->getMessage());
        }
    }

    public function getMahasiswa()
    {
        try {
            $decodeToken = parseJwt($this->request->header('Authorization'));
            $mahasiswa   = $decodeToken->user->mahasiswa;
            $data        = Mahasiswa::where('gugus_id', $mahasiswa->gugus_id)->get();

            if (!count($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                    'code'    => 404
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'List mahasiswa',
                'code'    => 200,
                'data'    => $data,
            ]);
        } catch (\Throwable $th) {
            return writeLog($th->getMessage());
        }
    }
}
