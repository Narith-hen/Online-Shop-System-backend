<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Get(
        path: '/api/payment-methods',
        summary: 'Get available payment methods with QR info',
        tags: ['Payment Methods'],
        responses: [
            new OA\Response(response: 200, description: 'List of payment methods'),
        ]
    )]
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
