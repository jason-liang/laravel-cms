<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

Route::get('{any}', [Controllers\PageController::class, 'get'])->where('any', '^(?!admin).*$');
