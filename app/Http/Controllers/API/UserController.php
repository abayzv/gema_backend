<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        return json_encode(url("") . "/storage/" . $selfiepath);
        // try {
        //     $request->validate([
        //         'name' => ['required', 'string', 'max:255'],
        //         'username' => ['required', 'string', 'max:255', 'unique:users'],
        //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //         'password' => ['required', 'string', new Password]
        //     ]);

        //     $users = User::create([
        //         'name' => $request->name,
        //         'email' => $request->email,
        //         'username' => $request->username,
        //         'phone' => $request->phone,
        //         'password' => Hash::make($request->password),
        //     ]);

        //     UserDetails::create([
        //         'user_id' => $users->id,
        //         'mother_name' => $request->mother_name,
        //         'address' => $request->address,
        //         'city' => $request->city,
        //         'districts' => $request->districts,
        //         'region' => $request->region,
        //         'post_code' => $request->post_code,
        //         'id_card_number' => $request->id_card_number,
        //         'id_card_path' => $request->id_card_path,
        //         'selfie_path' => $request->selfie_path,
        //         'npwp_number' => $request->npwp_number,
        //         'npwp_path' => $request->npwp_path,
        //         'account_name' => $request->account_name,
        //         'account_number' => $request->account_number,
        //         'bank_name' => $request->bank_name,
        //     ]);

        //     $user = User::with(['details'])->where('email', $request->email)->first();

        //     $tokenResult = $user->createToken('authToken')->plainTextToken;

        //     return ResponseFormatter::success([
        //         'access_token' => $tokenResult,
        //         'token_type' => 'Bearer',
        //         'user' => $user
        //     ], 'User Registered');
        // } catch (Exception $error) {
        //     return ResponseFormatter::error([
        //         'message' => 'Something went wrong',
        //         'error' => $error,
        //     ], 'Authentication Failed', 500);
        // }
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
