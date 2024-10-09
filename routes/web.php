<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FaqController;
//use Illuminate\Support\Facades\Request;
use App\Http\Controllers\CardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\RadiusController;
use App\Http\Controllers\RegiaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CampanhaController;
use App\Http\Controllers\AccessDataController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\LoginCustomizationController;
use Illuminate\Http\Request;
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

Route::get('/hotspot', function () {
    return Inertia::render('HotSpot');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*Route::get('/radius', function () {
    return Inertia::render('Radius');
})->middleware(['auth', 'verified'])->name('radius');*/


Route::middleware('auth')->group(function () {

   /* Route::get('/', function () {
        return Inertia::render('Home');
    })->name('home');*/

    Route::get('/', [CardController::class, 'index'])->middleware('check-page-access:ler')->name('home');

    Route::get('/edit-page/{id}', [PageController::class, 'edit'])->name('edit.page');
    Route::post('/update-page/{id}', [PageController::class, 'update'])->name('update.page');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/actions', [UserPermissionController::class, 'getActions']);
    Route::get('/pages', [UserPermissionController::class, 'getPages']);

    Route::get('users/{user}/permissions', [UserPermissionController::class, 'getPermissions']);
    Route::get('users/{user}/permissionsSelect', [UserPermissionController::class, 'getPermissionsSelect']);
    Route::post('users/{user}/permissions', [UserPermissionController::class, 'updatePermissions']);
    Route::post('/update-region-connection', [AccessDataController::class, 'updateRegionConnection'])->name('update.region.connection');
    Route::resource('accessData', AccessDataController::class);

   /* Route::post('/set-user-session/regiao', function (Request $request) {
        // Use o objeto $request para acessar a sessãod        
        session()->put('regiao', $request->input('regiao'));
        
        return response()->json(['success' => true]);
    })->name('set.user.session.regiao');*/

   



    Route::prefix('radios')->middleware('check-page-access:ler','set-dynamic-db')->group(function () {        
        Route::get('/', [RadioController::class, 'index'])->name('radios.index');   
        Route::get('/track', [RadioController::class, 'track'])->name('radios.track');     
        Route::get('/mapaRadio', [RadioController::class, 'getGeoRadio'])->name('radios.getGeoRadio');
        Route::get('/RelatoriosRadios', [RadioController::class, 'radioRelatorio'])->name('radios.RelatoriosRadios');
        Route::get('/{id}', [RadioController::class, 'show'])->name('radios.show');
        Route::post('/', [RadioController::class, 'store'])->middleware('check-page-access:gravar')->name('radios.store');  
        Route::post('/track', [RadioController::class, 'track'])->middleware('check-page-access:gravar')->name('radios.track');        
        Route::put('/{id}', [RadioController::class, 'update'])->middleware('check-page-access:atualizar')->name('radios.update');       
        Route::delete('/{id}', [RadioController::class, 'destroy'])->middleware('check-page-access:excluir')->name('radios.destroy');
        
    });

    Route::prefix('radius')->middleware('check-page-access:ler','set-dynamic-db')->group(function () {        
        Route::get('/', [RadiusController::class, 'index'])->name('radius.index');
        Route::get('/lista', [RadiusController::class, 'lista'])->name('radius.lista');
        Route::get('/{id}', [RadiusController::class, 'show'])->name('radius.show');
        Route::post('/', [RadiusController::class, 'store'])->middleware('check-page-access:gravar')->name('radius.store');  
        Route::put('/{id}', [RadiusController::class, 'update'])->middleware('check-page-access:atualizar')->name('radius.update');       
        Route::delete('/{id}', [RadiusController::class, 'destroy'])->middleware('check-page-access:excluir')->name('radius.destroy');
        
    });

    

    Route::prefix('controladora')->middleware('check-page-access:ler')->group(function () {        
        Route::get('/', [AccessDataController::class, 'indexControllers'])->name('controladora.index');
        Route::get('/{id}', [AccessDataController::class, 'show'])->name('controladora.show');
        Route::post('/', [AccessDataController::class, 'store'])->middleware('check-page-access:gravar')->name('controladora.store');  
        Route::put('/{id}', [AccessDataController::class, 'update'])->middleware('check-page-access:atualizar')->name('controladora.update');       
        Route::delete('/{id}', [AccessDataController::class, 'destroy'])->middleware('check-page-access:excluir')->name('controladora.destroy');
    });
    
    Route::prefix('database')->middleware('check-page-access:ler')->group(function () {        
        Route::get('/', [AccessDataController::class, 'indexDatabases'])->name('database.index');
        Route::get('/{id}', [AccessDataController::class, 'show'])->name('database.show');
        Route::post('/', [AccessDataController::class, 'store'])->middleware('check-page-access:gravar')->name('database.store');  
        Route::put('/{id}', [AccessDataController::class, 'update'])->middleware('check-page-access:atualizar')->name('database.update');       
        Route::delete('/{id}', [AccessDataController::class, 'destroy'])->middleware('check-page-access:excluir')->name('database.destroy');
    });
    
 
   Route::prefix('users')->middleware('check-page-access:ler')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/count', [UserController::class, 'count'])->name('users.count');
        Route::get('/new', [UserController::class, 'new'])->middleware('check-page-access:gravar')->name('users.create');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::post('/', [UserController::class, 'store'])->middleware('check-page-access:gravar')->name('users.store');
        Route::put('/{id}', [UserController::class, 'update'])->middleware('check-page-access:atualizar')->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('check-page-access:excluir')->name('users.destroy');       

        

    });
    Route::prefix('usuarios')->middleware('check-page-access:ler')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/new', [UsuarioController::class, 'new'])->name('usuarios.create');
        Route::get('/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::post('/', [UsuarioController::class, 'store'])->middleware('check-page-access:gravar')->name('usuarios.store');
        Route::put('/{id}', [UsuarioController::class, 'update'])->middleware('check-page-access:atualizar')->name('usuarios.update');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->middleware('check-page-access:excluir')->name('usuarios.destroy');
    });

    Route::prefix('campanhas')->middleware('check-page-access:ler')->group(function () {
        Route::get('/', [CampanhaController::class, 'index'])->name('campanhas.index');
        Route::get('/adicionar', [CampanhaController::class, 'new'])->name('campanhas.create');
        Route::get('/{id}', [CampanhaController::class, 'show'])->name('campanhas.show');
        Route::post('/', [CampanhaController::class, 'store'])->middleware('check-page-access:gravar')->name('campanhas.store');
        Route::put('/{id}', [CampanhaController::class, 'update'])->middleware('check-page-access:atualizar')->name('campanhas.update');
        Route::delete('/{id}', [CampanhaController::class, 'destroy'])->middleware('check-page-access:excluir')->name('campanhas.destroy');
    });

    Route::prefix('faq')->middleware('check-page-access:ler')->group(function (){
        Route::get('/', [FaqController::class, 'index'])->name('faq.index');
        Route::get('/{id}', [FaqController::class, 'show'])->name('faq.show');
        Route::post('/store', [FaqController::class, 'store'])->middleware('check-page-access:gravar')->name('faq.store');
        Route::put('/{id}', [FaqController::class, 'update'])->middleware('check-page-access:atualizar')->name('faq.update');
    });
    Route::prefix('regioes')->middleware('check-page-access:ler')->group(function (){
        Route::get('/', [RegiaoController::class, 'index'])->name('regioes.index');
        Route::get('/{id}', [RegiaoController::class, 'show'])->name('regioes.show');
        Route::post('/store', [RegiaoController::class, 'store'])->middleware('check-page-access:gravar')->name('regioes.store');
        Route::put('/{id}', [RegiaoController::class, 'update'])->middleware('check-page-access:atualizar')->name('regioes.update');
        Route::delete('/{id}', [RegiaoController::class, 'destroy'])->middleware('check-page-access:excluir')->name('regioes.destroy');
    });
    Route::prefix('login_customizations')->middleware('check-page-access:ler')->group(function () {     
        Route::get('/', [LoginCustomizationController::class, 'index'])->name('login_customizations.index');  
        Route::get('/create', [LoginCustomizationController::class, 'create'])->name('login_customizations.create');      
        Route::post('/', [LoginCustomizationController::class, 'store'])->middleware('check-page-access:gravar')->name('login_customizations.store');        
        Route::get('/{id}/edit', [LoginCustomizationController::class, 'edit'])->name('login_customizations.edit');       
        Route::put('/{id}', [LoginCustomizationController::class, 'update'])->middleware('check-page-access:atualizar')->name('login_customizations.update');      
        Route::delete('/{id}', [LoginCustomizationController::class, 'destroy'])->middleware('check-page-access:excluir')->name('login_customizations.destroy');       
        Route::get('/{id}', [LoginCustomizationController::class, 'show'])->name('login_customizations.show');
    });
    Route::resource('cards', CardController::class);
    Route::resource('notes', NoteController::class);
    //Route::resource('login_customizations', LoginCustomizationController::class)->middleware('check-page-access:ler');
    //Route::resource('regioes', RegiaoController::class);

});

require __DIR__.'/auth.php';
