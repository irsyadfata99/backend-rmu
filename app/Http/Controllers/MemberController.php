<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\WilayahCounter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * Get wilayah options for dropdown
     */
    public function getWilayahOptions()
    {
        $wilayahOptions = [
            'BDG' => 'Bandung',
            'JKT' => 'Jakarta', 
            'SBY' => 'Surabaya',
            'MDN' => 'Medan',
            'DPK' => 'Depok',
            'TGR' => 'Tangerang',
            'PLB' => 'Palembang',
            'SMG' => 'Semarang',
            'MKS' => 'Makassar',
            'BJM' => 'Banjarmasin'
        ];

        return response()->json([
            'success' => true,
            'data' => $wilayahOptions
        ]);
    }

    /**
     * Get next member ID for a wilayah
     */
    public function getNextMemberId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wilayah' => 'required|string|size:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $wilayah = strtoupper($request->wilayah);
        
        try {
            // Get or create wilayah counter
            $counter = WilayahCounter::firstOrCreate(
                ['wilayah_code' => $wilayah],
                ['current_number' => 0]
            );
            
            // Increment the counter
            $counter->increment('current_number');
            
            // Generate member ID: wilayah + 4-digit number
            $memberId = $wilayah . str_pad($counter->current_number, 4, '0', STR_PAD_LEFT);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'member_id' => $memberId,
                    'wilayah' => $wilayah,
                    'sequence_number' => $counter->current_number
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating member ID',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new member
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nikKtp' => 'required|string|size:16|unique:members,nik_ktp',
            'namaLengkap' => 'required|string|max:255',
            'alamatLengkap' => 'required|string',
            'wilayah' => 'required|string|size:3',
            'nomorWhatsapp' => 'required|string|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $wilayah = strtoupper($request->wilayah);
            
            // Get or create wilayah counter and increment
            $counter = WilayahCounter::firstOrCreate(
                ['wilayah_code' => $wilayah],
                ['current_number' => 0]
            );
            
            $counter->increment('current_number');
            
            // Generate member ID
            $memberId = $wilayah . str_pad($counter->current_number, 4, '0', STR_PAD_LEFT);
            
            // Create member
            $member = Member::create([
                'member_id' => $memberId,
                'nik_ktp' => $request->nikKtp,
                'nama_lengkap' => $request->namaLengkap,
                'alamat_lengkap' => $request->alamatLengkap,
                'wilayah' => $wilayah,
                'nomor_whatsapp' => $request->nomorWhatsapp,
                'status' => 'active',
                'registration_date' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member registered successfully',
                'data' => $member
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all members (protected route)
     */
    public function index(Request $request)
    {
        try {
            $query = Member::query();
            
            // Add filtering if needed
            if ($request->has('wilayah')) {
                $query->where('wilayah', $request->wilayah);
            }
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            $members = $query->orderBy('registration_date', 'desc')->paginate(15);
            
            return response()->json([
                'success' => true,
                'data' => $members
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching members',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member statistics (protected route)
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_members' => Member::count(),
                'active_members' => Member::where('status', 'active')->count(),
                'inactive_members' => Member::where('status', 'inactive')->count(),
                'members_by_wilayah' => Member::select('wilayah', DB::raw('count(*) as total'))
                    ->groupBy('wilayah')
                    ->get()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific member (protected route)
     */
    public function show($id)
    {
        try {
            $member = Member::where('member_id', $id)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => $member
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }
    }

    /**
     * Update member (protected route)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'namaLengkap' => 'sometimes|required|string|max:255',
            'alamatLengkap' => 'sometimes|required|string',
            'nomorWhatsapp' => 'sometimes|required|string|max:15',
            'status' => 'sometimes|required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $member = Member::where('member_id', $id)->firstOrFail();
            $member->update($request->only([
                'nama_lengkap', 'alamat_lengkap', 'nomor_whatsapp', 'status'
            ]));
            
            return response()->json([
                'success' => true,
                'message' => 'Member updated successfully',
                'data' => $member
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete member (protected route)
     */
    public function destroy($id)
    {
        try {
            $member = Member::where('member_id', $id)->firstOrFail();
            $member->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Member deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}