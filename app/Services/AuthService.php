<?php
namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Auth;

class AuthService {
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register($params)
    {
        try {            
            return $this->user->create($params);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    public function login($params)
    {
        $user = $this->user->where('email', $params['email'])->first();

        $isPasswordValid = Hash::check($params['password'], $user->password);

        if (!$isPasswordValid) {
            return [
                'status' => false,
                'message' => 'Invalid password and email',
            ];
        }

        $token = $user->createToken('user')->plainTextToken;

        return [
            'status' => true,
            'access_token' => $token,
            'name' => $user->name,
        ];
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            
            if ($user) {
                $user->currentAccessToken()->delete(); 
                return [
                    'status' => true,
                    'message' => 'Logout successful',
                ];
            }

            return [
                'status' => false,
                'message' => 'No user logged in',
            ];
        } catch (Exception $e) {
            Log::error($e);
            return [
                'status' => false,
                'message' => 'Logout failed',
            ];
        }
    }
    
}
