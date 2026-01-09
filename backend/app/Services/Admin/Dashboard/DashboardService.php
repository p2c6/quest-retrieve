<?php
namespace App\Services\Admin\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardService {
    /**
     * Get total users per month count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getUsersPerMonth(): JsonResponse
    {
        try {
        $rawData = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyUsers = array_fill(0, 12, 0);

        foreach ($rawData as $row) {
            $monthlyUsers[$row->month - 1] = $row->total;
        }

        return response()->json([
            'message' => 'Users per month count successfully retrieved.',
            'data' => $monthlyUsers
        ], 200);
        } catch(\Throwable $th) {
            info("Error on sending email verification notification: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during sending email verification notification.'
            ], 500);
        }
    }

    /**
     * Get verified & not yet verified users count.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getVerifiedUsers(): JsonResponse
    {
        $verified = User::whereNotNull('email_verified_at')->count();
        $notYetVerified = User::whereNull('email_verified_at')->count();

        return response()->json([
            'message' => 'Verified & Unverified users count successfully retrieved.',
            'data' => [$verified, $notYetVerified]
        ], 200);
    }
}