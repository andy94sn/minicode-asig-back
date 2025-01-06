<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function callback(Request $request)
    {
        try {
            $key  = env('MAIB_SIGNATURE_KEY');
            $json = file_get_contents('php://input');
            Log::info(print_r($json, true));
            $data = json_decode($json, true);
            $sortedDataByKeys = $this->sortByKeyRecursive($data['result']);
            $sortedDataByKeys[] = $key;
            $signString = $this->implodeRecursive($sortedDataByKeys);
            $sign = base64_encode(hash('sha256', $signString, true));
            $status = null;

            Log::info(print_r($data, true));

            if($sign === $data['signature']){
                $result = $data['result'];
                $transaction = DB::table('transactions')->where('pay_id', $result['payId'])->first();

                switch($result['status']){
                    case 'CREATED' :  $status = 'canceled'; break;
                    case 'PENDING' :  $status = 'pending'; break;
                    case 'OK'      :  $status = 'completed'; break;
                    case 'FAILED'  :  $status = 'failed'; break;
                    case 'TIMEOUT' :  $status = 'expired'; break;
                }
                Log::info($status);

                if($transaction){
                    $order = Order::find($transaction->order_id);
                    if($order){
                        $order->status = $status;
                        $order->save();
                    }
                }

                if($status === 'completed'){
                    Log::info('Callback successfully');
                    return response()->json(['status' => true], 200);
                }

            }else{
                Log::error('Error: Signature is invalid');
            }


            return response()->json(['status' => false], 400);
        }catch(\Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
            return response()->json(['status' => false], 500);
        }
    }


    private  function implodeRecursive($array): string
    {
        $result = '';
        foreach ($array as $item) {
            $result .= (is_array($item) ? $this->implodeRecursive(':', $item) : (string)$item) . ':';
        }

        return substr($result, 0, -1);
    }

    private  function sortByKeyRecursive(array $array): array
    {
        ksort($array, SORT_STRING);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->sortByKeyRecursive($value);
            }
        }
        return $array;
    }
}
