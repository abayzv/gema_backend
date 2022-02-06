<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\MailActivators;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function fetch(Request $request)
    {
        $user = User::with(['details'])->find(1);
        return json_encode($user);
        // return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::with(['details'])->where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {
        $checkEmail = User::where('email',$request->email)->orWhere('phone',$request->phone)->first();
        if($checkEmail == null){
            try {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'mother_name' => ['required', 'string'],
                    'address' => ['required', 'string'],
                    'city' => ['required', 'string'],
                    'districts' => ['required', 'string'],
                    'region' => ['required', 'string'],
                    'post_code' => ['required', 'integer'],
                    'id_card_number' => ['required', 'string'],
                    'id_card_path' => ['required','image','mimes:jpg,png,jpeg'],
                    'selfie_path' => ['required','image','mimes:jpg,png,jpeg'],
                    'npwp_number' => ['required', 'string'],
                    'npwp_path' => ['required','image','mimes:jpg,png,jpeg'],
                    'account_number' => ['required', 'string'],
                    'bank_name' => ['required', 'string'],
                    'password' => ['required', 'string', new Password]
                ]);
                // Upload Selfie Image
                $selfie = $request->file('selfie_path');
                $selfiename = $selfie->getClientOriginalName();
                $finalSelfie = date('His') . $selfiename;
                $selfiepath = $request->file('selfie_path')->storeAs('images', $finalSelfie, 'public');
    
                // Upload KTP Image
                $idCard = $request->file('id_card_path');
                $idCardname = $idCard->getClientOriginalName();
                $finalIdCard = date('His') . $idCardname;
                $idCardpath = $request->file('id_card_path')->storeAs('images', $finalIdCard, 'public');
    
                // Upload KTP Image
                $npwpId = $request->file('npwp_path');
                $npwpIdname = $npwpId->getClientOriginalName();
                $finalNpwpId = date('His') . $npwpIdname;
                $npwpIdpath = $request->file('npwp_path')->storeAs('images', $finalNpwpId, 'public');
    
                $users = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    // 'username' => $request->username,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                ]);
    
                UserDetails::create([
                    'user_id' => $users->id,
                    'mother_name' => $request->mother_name,
                    'address' => $request->address,
                    'city' => $request->city,
                    'districts' => $request->districts,
                    'region' => $request->region,
                    'post_code' => $request->post_code,
                    'id_card_number' => $request->id_card_number,
                    'id_card_path' => url("") . '/storage/' . $idCardpath,
                    'selfie_path' => url("") . '/storage/' . $selfiepath,
                    'npwp_number' => $request->npwp_number,
                    'npwp_path' => url("") . '/storage/' . $npwpIdpath,
                    'account_name' =>$request->name,
                    'account_number' => $request->account_number,
                    'bank_name' => $request->bank_name,
                ]);
    
                $user = User::with(['details'])->where('email', $request->email)->first();
    
                $tokenResult = $user->createToken('authToken')->plainTextToken;
    
    
    
                $details = [
                    'name' => $request->name,
                    'link_activation' => 'http://localhost:8000/mailactivate?name=' . $request->email . "&token=" . $tokenResult
                ];
                Mail::to($request->email)->send(new \App\Mail\MyTestMail($details));
    
                MailActivators::create([
                    'name' => $request->email,
                    'token' => $tokenResult,
                ]);
    
    
                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user
                ], 'User Registered');
            } catch (Exception $error) {
                return response('Data yang anda masukan tidak valid, silahkan cek kembali data yang anda masukan', 403);
            }
        }else{
            return response('Email / Nomor Telphone Sudah Tedaftar', 403);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user, 'Profile Updated');
    }
}
