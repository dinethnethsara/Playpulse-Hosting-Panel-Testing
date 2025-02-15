<?php

namespace PlayPulse\Http\Controllers\Api;

use PlayPulse\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PlayPulse\Models\User;

class AuthController extends Controller
{
	public function login(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required|string'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		if (!Auth::attempt($request->only('email', 'password'))) {
			return response()->json([
				'message' => 'Invalid credentials'
			], 401);
		}

		$user = User::where('email', $request->email)->first();
		$token = $user->createToken('api-token')->plainTextToken;

		return response()->json([
			'access_token' => $token,
			'token_type' => 'Bearer',
			'user' => $user
		]);
	}

	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();
		return response()->json(['message' => 'Successfully logged out']);
	}

	public function refresh(Request $request)
	{
		$user = $request->user();
		$user->tokens()->delete();
		$token = $user->createToken('api-token')->plainTextToken;

		return response()->json([
			'access_token' => $token,
			'token_type' => 'Bearer'
		]);
	}

	public function me(Request $request)
	{
		return response()->json([
			'user' => $request->user(),
			'permissions' => $request->user()->permissions
		]);
	}

	public function updatePassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'current_password' => 'required|string',
			'password' => 'required|string|min:8|confirmed'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		$user = $request->user();

		if (!Hash::check($request->current_password, $user->password)) {
			return response()->json(['message' => 'Current password is incorrect'], 401);
		}

		$user->password = Hash::make($request->password);
		$user->save();

		return response()->json(['message' => 'Password updated successfully']);
	}
}