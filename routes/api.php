<?php

use App\Http\Controllers\Admin\NotebookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando!']);
});

Route::prefix('notebook')->group(function () {
    Route::post('/login', [NotebookController::class, 'apiLogin']);
    Route::post('/heartbeat', [NotebookController::class, 'apiHeartbeat']);
    Route::get('/{notebookId}/comandos', [NotebookController::class, 'apiComandos']);
    Route::post('/midia', [NotebookController::class, 'apiMidia']);
});