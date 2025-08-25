<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use App\Models\WilayahCounter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Member::query();

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                      ->orWhere('member_id', 'LIKE', "%{$search}%")
                      ->orWhere('nik_ktp', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('wilayah')) {
                $query->where('wilayah', $request->wilayah);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('registration_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('registration_date', '<=', $request->date_to);
            }

            // Pagination
            $perPage = $request->per_page ?? 10;
            $members = $query->orderBy('registration_date', 'desc')
                           ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data anggota berhasil diambil',
                'data' => $members
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new member
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get wilayah info
            $wilayahCounter = WilayahCounter::where('wilayah_code', $request->wilayah)->first();
            
            if (!$wilayahCounter) {
                throw new Exception('Wilayah tidak ditemukan');
            }

            // Generate member ID
            $memberId = Member::generateMemberId($request->wilayah);

            // Create member
            $member = Member::create([
                'member_id' => $memberId,
                'nik_ktp' => $request->nikKtp,
                'nama_lengkap' => $request->namaLengkap,
                'alamat_lengkap' => $request->alamatLengkap,
                'wilayah' => $request->wilayah,
                'wilayah_name' => $wilayahCounter->wilayah_name,
                'nomor_whatsapp' => $request->nomorWhatsapp,
                'status' => 'active',
                'registration_date' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran anggota berhasil',
                'data' => [
                    'member' => $member,
                    'member_id' => $memberId
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified member
     */
    public function show(string $id): JsonResponse
    {
        try {
            $member = Member::where('member_id', $id)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anggota tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data anggota berhasil diambil',
                'data' => $member
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $member = Member::where('member_id', $id)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anggota tidak ditemukan'
                ], 404);
            }

            // Validation rules for update (NIK can be same as current)
            $rules = [
                'namaLengkap' => 'sometimes|string|min:3|max:255',
                'alamatLengkap' => 'sometimes|string|min:10|max:1000',
                'nomorWhatsapp' => 'sometimes|string|min:10|max:15|regex:/^[0-9]+$/',
                'status' => 'sometimes|in:active,inactive'
            ];

            $request->validate($rules);

            $member->update($request->only([
                'nama_lengkap' => $request->namaLengkap,
                'alamat_lengkap' => $request->alamatLengkap,
                'nomor_whatsapp' => $request->nomorWhatsapp,
                'status' => $request->status
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Data anggota berhasil diupdate',
                'data' => $member
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified member
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $member = Member::where('member_id', $id)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anggota tidak ditemukan'
                ], 404);
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil dihapus'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wilayah options
     */
    public function getWilayahOptions(): JsonResponse
    {
        try {
            $wilayahOptions = WilayahCounter::getWilayahOptions();

            return response()->json([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil',
                'data' => $wilayahOptions
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data wilayah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next member ID for preview
     */
    public function getNextMemberId(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'wilayah' => 'required|string|size:3|exists:wilayah_counters,wilayah_code'
            ]);

            $nextMemberId = WilayahCounter::getNextMemberId($request->wilayah);

            return response()->json([
                'success' => true,
                'message' => 'ID anggota berikutnya berhasil diambil',
                'data' => [
                    'next_member_id' => $nextMemberId
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ID anggota berikutnya',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $totalMembers = Member::count();
            $activeMembers = Member::where('status', 'active')->count();
            $inactiveMembers = Member::where('status', 'inactive')->count();
            
            $membersByWilayah = Member::select('wilayah', 'wilayah_name', DB::raw('COUNT(*) as total'))
                                    ->groupBy('wilayah', 'wilayah_name')
                                    ->get()
                                    ->pluck('total', 'wilayah_name')
                                    ->toArray();

            $recentRegistrations = Member::whereDate('registration_date', '>=', now()->subDays(30))
                                        ->count();

            return response()->json([
                'success' => true,
                'message' => 'Statistik anggota berhasil diambil',
                'data' => [
                    'total_members' => $totalMembers,
                    'active_members' => $activeMembers,
                    'inactive_members' => $inactiveMembers,
                    'members_by_wilayah' => $membersByWilayah,
                    'recent_registrations' => $recentRegistrations
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}