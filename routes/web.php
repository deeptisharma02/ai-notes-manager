<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('notes');
});

// Interactive API documentation (Swagger UI rendering /openapi.yaml).
Route::get('/api/docs', function () {
    return view('docs');
});
