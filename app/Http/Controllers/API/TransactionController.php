<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');

        if ($id) {
            $transaction = Transaction::with(['items.product'])->find($id);

            if ($transaction)
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
        }

        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if ($status)
            $transaction->where('status', $status);

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        // return json_encode($request->all());
        $check = Transaction::where('payment_id', $request->slug."2022")->first();
        if($check === null){
            $request->validate([
                'idProject' => 'required|integer',
                'jumlahKoin' => 'required|integer',
                'jumlahLot' => 'required|integer',
                'total' => 'required|integer',
            ]);
    
            $transaction = Transaction::create([
                'payment_id' => $request->slug."2022",
                'users_id' => Auth::user()->id,
                'project_id' => $request->idProject,
                'total_koin' => $request->jumlahKoin,
                'total_lot' => $request->jumlahLot,
                'total_price' => $request->total,
                'status' => "pending"
            ]);
    
            return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');
        
        }else{
            return response('Error', 409);
        }

       }
}
