<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta de prueba
Route::apiResource('users', UserController::class);
?>
