<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AgentController;
use App\Models\Agent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;




class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'me']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['agentUser', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Agent unauthorized or password invalid',
                ],
                401
            );
        }

        // Asignar el token al agente
        $agent = Agent::where('agentUser', $credentials['agentUser'])->first();
        $agent->agentToken = $token;
        $agent->agentStatus = 'online';
        $agent->save();

        $message = 'Agent logged in successfully';
        return $this->respondWithToken($token, null, $message);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {

        $response = auth()->user();
        if (!$response) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent unauthorized or token invalid'
            ], 401);
        }

        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     * En el request obtendra el agentUser que se deslogueara, para asi actualizar el estado del agente y borrar el token
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $hasBearer = $request->headers->has('Authorization');

        if (!$hasBearer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $authorizationHeader = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        auth()->logout();

        $agent = Agent::where('agentUser', $request->agentUser)->first();
        $agent->agentToken = '';
        $agent->agentStatus = 'offline';
        $agent->save();

        return response()->json(['message' => 'Agent successfully logged out']);
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  string $agent
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $agent = null, $message = null)
    {
        if (!$agent) {
            $agent = auth()->user();
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'id' => $agent->_id,
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60 * 10, // 10 hours
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'operation' => $agent->operation,
                    'agentUser' => $agent->agentUser,
                    'agentStatus' => $agent->agentStatus,
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60 * 10, // 10 hours
                ]
            ]);
        }
    }


    /**
     * Register a new userAgent.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $agentController = new AgentController();

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:agents',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'name' => 'required|string',
            'operation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid data provided',
                    'errors' => $validator->errors()
                ],
                422
            );
        }

        $agent = Agent::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'operation' => $request->operation,
            'agentUser' => $agentController->creategentUser(),
            'agentToken' => '',
            'agentStatus' => 'offline',
            'skills' => [],
            'capacity' => 0,
            'busy' => false,           

        ]);

        $token = JWTAuth::fromUser($agent); // Generar el token para el agente y guardarlo en la base de datos

        // Asignar el token al agente recién creado
        $agent->agentToken = $token;
        $agent->save();

        $message = 'User registered successfully';

        return $this->respondWithToken($token, $agent, $message);
    }
}
