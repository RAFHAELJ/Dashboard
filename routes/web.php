<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\RegiaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CampanhaController;
use App\Http\Controllers\LoginCustomizationController;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/radius', function () {
    return Inertia::render('Radius');
})->middleware(['auth', 'verified'])->name('radius');


Route::middleware('auth')->group(function () {

   /* Route::get('/', function () {
        return Inertia::render('Home');
    })->name('home');*/

    Route::get('/', [CardController::class, 'index'])->name('home');

    Route::get('/edit-page/{id}', [PageController::class, 'edit'])->name('edit.page');
    Route::post('/update-page/{id}', [PageController::class, 'update'])->name('update.page');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    


    Route::prefix('radios')->group(function () {        
        Route::get('/', [RadioController::class, 'index'])->name('radios.index');   
        Route::get('/track', [RadioController::class, 'track'])->name('radios.track');     
        Route::get('/mapaRadio', [RadioController::class, 'getGeoRadio'])->name('radios.getGeoRadio');
        Route::get('/RelatoriosRadios', [RadioController::class, 'radioRelatorio'])->name('radios.RelatoriosRadios');
        Route::get('/{id}', [RadioController::class, 'show'])->name('radios.show');
        Route::post('/', [RadioController::class, 'store'])->name('radios.store');  
        Route::post('/track', [RadioController::class, 'track'])->name('radios.track');        
        Route::put('/{id}', [RadioController::class, 'update'])->name('radios.update');       
        Route::delete('/{id}', [RadioController::class, 'destroy'])->name('radios.destroy');
        
    });
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/count', [UserController::class, 'count'])->name('users.count');
        Route::get('/new', [UserController::class, 'new'])->name('users.create');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/new', [UsuarioController::class, 'new'])->name('usuarios.create');
        Route::get('/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::post('/', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::prefix('campanhas')->group(function () {
        Route::get('/', [CampanhaController::class, 'index'])->name('campanhas.index');
        Route::get('/adicionar', [CampanhaController::class, 'new'])->name('campanhas.create');
        Route::get('/{id}', [CampanhaController::class, 'show'])->name('campanhas.show');
        Route::post('/', [CampanhaController::class, 'store'])->name('campanhas.store');
        Route::put('/{id}', [CampanhaController::class, 'update'])->name('campanhas.update');
        Route::delete('/{id}', [CampanhaController::class, 'destroy'])->name('campanhas.destroy');
    });

    Route::prefix('faq')->group(function (){
        Route::get('/', [FaqController::class, 'index'])->name('faq.index');
        Route::get('/{id}', [FaqController::class, 'show'])->name('faq.show');
        Route::post('/store', [FaqController::class, 'store'])->name('faq.store');
        Route::put('/{id}', [FaqController::class, 'update'])->name('faq.update');
    });
    Route::prefix('regioes')->group(function (){
        Route::get('/', [RegiaoController::class, 'index'])->name('regioes.index');
        Route::get('/{id}', [RegiaoController::class, 'show'])->name('regioes.show');
        Route::post('/store', [RegiaoController::class, 'store'])->name('regioes.store');
        Route::put('/{id}', [RegiaoController::class, 'update'])->name('regioes.update');
        Route::delete('/{id}', [RegiaoController::class, 'destroy'])->name('regioes.destroy');
    });
    Route::resource('cards', CardController::class);
    Route::resource('notes', NoteController::class);
    Route::resource('login_customizations', LoginCustomizationController::class);
    //Route::resource('regioes', RegiaoController::class);

});

require __DIR__.'/auth.php';
