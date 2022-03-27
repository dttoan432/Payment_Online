<?php

namespace App\Http\Controllers;

use App\Models\PaymentMomo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function showVnpay() {
        return view('paymentVnpay');
    }

    public function storeVnpay(Request $request) {
        $inputData = [
            "vnp_Version"       => "2.0.1",
            "vnp_TmnCode"       => env('VNP_TMN_CODE'),
            "vnp_Amount"        => (int)$request->amount * 100,
            "vnp_Command"       => "pay",
            "vnp_CreateDate"    => date('YmdHis'),
            "vnp_CurrCode"      => "VND",
            "vnp_IpAddr"        => $_SERVER['REMOTE_ADDR'],
            "vnp_Locale"        => $request->language,
            "vnp_OrderInfo"     => $request->order_info,
            "vnp_OrderType"     => $request->order_type,
            "vnp_ReturnUrl"     => env('VNP_RETURN_URL'),
            "vnp_TxnRef"        => $request->order_id,
            "vnp_BankCode"      => $request->bank_code
        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = env('VNP_URL') . "?" . $query;
        $vnpSecureHash = hash('sha256', env('VNP_HASHSECRET') . $hashdata);
        $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;

        return redirect($vnp_Url);
    }

    public function returnVnpay(Request $request) {
        return view('paymentVnpayReturn', [
            'request' => $request,
        ]);
    }

    public function getListMomo() {
        $order = PaymentMomo::orderBy('created_at', 'DESC')->get();

        return view('paymentMomo', [
            'order' => $order
        ]);
    }

    public function showMomo() {
        return view('createPaymentMomo');
    }

    public function paymentMomo(Request $request)
    {
        try {
            $accessKey = env('MOMO_ACCESS_KEY');
            $secretKey = env('MOMO_SECRET_KEY');

            $partnerCode = env('MOMO_PARTNER_CODE');
            $partnerName = env('MOMO_PARTNER_NAME');
            $storeId = env('MOMO_STORE_ID');
            $requestId = Str::uuid()->toString();
            $amount = $request->amount;                      // Tổng số tiền
            $orderId = $request->order_id;                   // Mã đơn hàng
            $orderInfo = $request->order_info;               // Nội dung thanh toán
            $autoCapture = false;
            $redirectUrl = env('MOMO_REDIRECT_URL');    // url trở về sau khi khách hàng thanh toán
            $ipnUrl = env('MOMO_IPN_URL');              // url momo trả về server để xử lý sau khi khách hàng thanh toán
            $requestType = 'captureWallet';
            $extraData = '';
            $lang = $request->language;
            $signature = hash_hmac('sha256', 'accessKey='.$accessKey.'&amount='.$amount.
                '&extraData='.$extraData. '&ipnUrl='.$ipnUrl.'&orderId='.$orderId.'&orderInfo='.$orderInfo.
                '&partnerCode='.$partnerCode. '&redirectUrl='.$redirectUrl.'&requestId='.$requestId.
                '&requestType='.$requestType, $secretKey);

            $header = ['Content-Type'  => 'application/json'];
            $data = [
                "partnerCode"   => $partnerCode,
                "partnerName"   => $partnerName,
                "storeId"       => $storeId,
                "requestType"   => $requestType,
                "ipnUrl"        => $ipnUrl,
                "redirectUrl"   => $redirectUrl,
                "orderId"       => $orderId,
                "amount"        => $amount,
                "lang"          => $lang,
                "autoCapture"   => $autoCapture,
                "orderInfo"     => $orderInfo,
                "requestId"     => $requestId,
                "extraData"     => $extraData,
                "signature"     => $signature
            ];

            $this->storePaymentMomo($data);

            $payload = json_encode($data);
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', env('MOMO_URL_CREATE_PAYMENT'), [
                'headers'   => $header,
                'body'      => $payload
            ])->getBody()->getContents();
            $response = json_decode($response, true);

            if ($response['resultCode'] == 0) {
                Log::info($response);
                return redirect($response['payUrl']);
            }
        } catch (Exception $e) {
            Log::error('Error payment momo', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

    public function paymentMomoConfirm(Request $request) {
        try {
            $data = $request->all();
            $payment = PaymentMomo::where('code', $data['orderId'])-> first();

            Log::info($data);
            if ($payment) {
                $accessKey = env('MOMO_ACCESS_KEY');
                $secretKey = env('MOMO_SECRET_KEY');

                $partnerCode = env('MOMO_PARTNER_CODE');
                $requestId = $data['requestId'];
                $orderId = $data['orderId'];
                $requestType = ($data['resultCode'] == 9000) ? 'capture' : 'cancel';
                $lang = 'vi';
                $amount = $data['amount'];
                $description = '';
                $signature = hash_hmac('sha256', 'accessKey='.$accessKey.'&amount='.$amount.
                    '&description='.$description. '&orderId='.$orderId.'&partnerCode='.$partnerCode.
                    '&requestId='.$requestId.'&requestType='.$requestType, $secretKey);

                $header = ['Content-Type'  => 'application/json'];
                $data = [
                    "partnerCode"   => $partnerCode,
                    "requestId"     => $requestId,
                    "orderId"       => $orderId,
                    "requestType"   => $requestType,
                    "lang"          => $lang,
                    "amount"        => $amount,
                    "description"   => $description,
                    "signature"     => $signature
                ];
                $payload = json_encode($data);

                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', env('MOMO_URL_CONFIRM_PAYMENT'), [
                    'headers'   => $header,
                    'body'      => $payload
                ])->getBody()->getContents();

                $response = json_decode($response, true);

                $payment->status = ($response['resultCode'] == 0 && $response['requestType'] == 'capture') ? PaymentMomo::STATUS['SUCCESS'] : PaymentMomo::STATUS['FAILURE'];
                $payment->time = $response['responseTime'];
                $payment->save();
            }
        } catch (Exception $e) {
            Log::error('Error confirm payment momo', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

    private function storePaymentMomo($data) {
        $payment = new PaymentMomo();
        $payment->code = $data['orderId'];
        $payment->money = $data['amount'];
        $payment->content = $data['orderInfo'];
        $payment->request_id = $data['requestId'];
        $payment->status = PaymentMomo::STATUS['UNPAID'];
        $payment->time = Carbon::now()->timestamp;
        $payment->save();
    }
}
