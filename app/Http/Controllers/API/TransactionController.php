<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');
        // return json_encode($id);

        if ($id) {
            $transaction = Transaction::with(['user', 'project'])->where('payment_id', $id)->first();

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

        $transaction = Transaction::with(['user', 'project'])->where('users_id', Auth::user()->id);

        if ($status)
            $transaction->where('status', $status);

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }

    public function show(Request $request)
    {
        $checkadmin = User::where('id', Auth::user()->id)->first();

        if ($checkadmin->roles) {
            $transaction = Transaction::with(['user', 'project'])->get();
            return ResponseFormatter::success(
                $transaction,
                'Data transaksi berhasil diambil'
            );
        } else {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
    }

    public function approve(Request $request)
    {

        $checkadmin = User::where('id', Auth::user()->id)->first();

        if ($checkadmin->roles) {
            $transaction = Transaction::find($request->input('id'));
            $transaction->status = 'paid';
            $transaction->save();

            return ResponseFormatter::success(
                $transaction,
                'Data transaksi berhasil di approve'
            );
        } else {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
    }

    public function uploadBukti(Request $request)
    {
        // Upload Selfie Image
        $img = $request->file('bukti_transfer');
        $imgname = $img->getClientOriginalName();
        $finalimg = date('His') . $imgname;
        $imgpath = $request->file('bukti_transfer')->storeAs('images', $finalimg, 'public');


        $transaction = Transaction::find($request->input('id'));
        $transaction->bukti_bayar = url("") . '/storage/' . $imgpath;
        $transaction->save();

        return ResponseFormatter::success(
            $transaction,
            'Data transaksi berhasil di approve'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        // return json_encode($request->all());
        $check = Transaction::where('payment_id', $request->slug . "2022")->first();

        if ($check === null) {
            $request->validate([
                'idProject' => 'required|integer',
                'jumlahKoin' => 'required|integer',
                'jumlahLot' => 'required|integer',
                'total' => 'required|integer',
            ]);

            $checkmember = User::with('details')->where('id', Auth::user()->id)->first();
            if ($checkmember->details->member_activated_at) {
                $transaction = Transaction::create([
                    'payment_id' => $request->slug . "2022",
                    'users_id' => Auth::user()->id,
                    'project_id' => $request->idProject,
                    'total_koin' => $request->jumlahKoin,
                    'total_lot' => $request->jumlahLot,
                    'total_price' => $request->total,
                    'is_member' => true,
                    'kode_unik' => rand(100, 999),
                    'status' => "pending"
                ]);
            } else {
                $transaction = Transaction::create([
                    'payment_id' => $request->slug . "2022",
                    'users_id' => Auth::user()->id,
                    'project_id' => $request->idProject,
                    'total_koin' => $request->jumlahKoin,
                    'total_lot' => $request->jumlahLot,
                    'total_price' => $request->total,
                    'is_member' => false,
                    'kode_unik' => rand(100, 999),
                    'status' => "pending"
                ]);
            }

            return ResponseFormatter::success($transaction, 'Transaksi berhasil');
        } else {
            return response('Error', 409);
        }
    }
}
