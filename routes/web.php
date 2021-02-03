<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PreviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/src/{file}', [FileController::class, 'proxy'])
    ->where('file', '.*');

Route::get('/video/{file}', [PreviewController::class, 'video'])
    ->where('file', '.*');
Route::get('/view/{file}', [PreviewController::class, 'view'])
    ->where('file', '.*');

Route::get('/thumbnail@{scale}x/{file}', [FileController::class, 'thumbnail'])
    ->whereNumber('scale')
    ->where('file', '.*');

Route::get('/', [GalleryController::class, 'index']);
Route::get('/{dir}', [GalleryController::class, 'index'])
    ->where('dir', '.*');
