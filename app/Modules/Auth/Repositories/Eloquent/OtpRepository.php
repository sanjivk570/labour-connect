<?php

namespace App\Modules\Auth\Repositories\Eloquent;

use App\Models\OtpVerification;
use App\Modules\Auth\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OtpRepository implements OtpRepositoryInterface
{
    public function create(array $data): OtpVerification
    {
        return OtpVerification::create($data);
    }

    public function latestActive(string $mobile, string $purpose): ?OtpVerification
    {
        return OtpVerification::where('mobile', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    public function incrementAttempts(OtpVerification $otp): void
    {
        DB::table('otp_verifications')->where('id', $otp->id)->increment('attempts');
        $otp->refresh();
    }

    public function markVerified(OtpVerification $otp): void
    {
        $otp->forceFill(['verified_at' => now()])->save();
    }
}
