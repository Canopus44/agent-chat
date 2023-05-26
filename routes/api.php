<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ConversationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Este archivo contiene las rutas API de tu aplicación. Estas rutas son cargadas por el RouteServiceProvider 
| dentro de un grupo al que se le asigna el middleware "api". ¡Disfruta construyendo tu API!
|
*/

// Esta ruta devuelve la información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Rutas API Agent
 */

//  Group auth
Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'auth'
    ],
    function ($router) {

        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('register', [AuthController::class, 'register']);
    }
);

// Group agent
Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'agent'
    ],
    function ($router) {

        Route::get('index', [AgentController::class, 'index']);
        Route::get('show/{id}', [AgentController::class, 'show']);
        Route::put('update/{id}', [AgentController::class, 'update']);
        Route::delete('delete/{id}', [AgentController::class, 'delete']);
    }
);


/**
 * Rutas API Conversation
 */
// Group conversation

Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'conversation'
    ],
    function ($router) {

        Route::get('index', [ConversationController::class, 'index']);
        Route::get('showByAgent/{agent_id}', [ConversationController::class, 'showByAgent']);
        Route::get('showMessagesByConversation/{session_chat}', [ConversationController::class, 'showMessagesByConversation']);
        Route::put('update/{id}', [ConversationController::class, 'update']);
        Route::delete('delete/{id}', [ConversationController::class, 'delete']);
    }
);
