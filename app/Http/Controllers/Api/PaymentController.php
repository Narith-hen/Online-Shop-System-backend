<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function methods()
    {
        $methods = [
            [
                'code'     => 'aba',
                'name'     => 'ABA KHQR',
                'qr_url'   => $this->qrUrl('aba.png'),
                'instructions' => 'Open ABA Mobile app → Scan QR → Pay → Upload screenshot',
            ],
        ];

        return response()->json(['data' => $methods]);
    }

    private function qrUrl(string $filename): string
    {
        if (Storage::disk('public')->exists('qr/' . $filename)) {
            return url(Storage::url('qr/' . $filename));
        }
        return null;
    }
}
