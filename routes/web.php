<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
//use Illuminate\Support\Facades\Request;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\FormController;
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
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\Hotspot\HotspotController;
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

Route::get('/hotspot', function () {
    return Inertia::render('HotSpot');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('hotspot/{region}')->middleware(['web','dynamic-db-hotspot','hotspot.session'])->group(function () {
    Route::get('/login', [HotspotController::class, 'showLoginForm'])->name('hotspot.login');
    Route::get('/logon/{id}/{campanha_id}', [HotspotController::class, 'logon'])->name('hotspot.logon');
    Route::get('/new/{id}', [HotspotController::class, 'new'])->name('hotspot.new');
    Route::get('/faq/{id}', [HotspotController::class, 'showFaq'])->name('hotspot.faq');
    Route::post('/authenticate', [HotspotController::class, 'authenticate'])->name('hotspot.authenticate');
    Route::post('/register', [HotspotController::class, 'register'])->name('hotspot.register');
    Route::get('/logout', [HotspotController::class, 'logout'])->name('hotspot.logout');

    Route::get('/wfd/login', [HotspotController::class, 'showRadiusLoginForm'])->name('hotspot.radius.login');
    Route::post('/wfd/authenticate', [HotspotController::class, 'authenticateRadius'])->name('hotspot.authenticate.radius');
    Route::get('/wfd/logout', [HotspotController::class, 'logoutRadius'])->name('hotspot.radius.logout');

});




Route::middleware('auth')->group(function () {



    Route::get('/', [CardController::class, 'index'])->middleware('check-page-access:ler')->name('home');

    Route::get('/export', [RadioController::class, 'export'])->middleware('set-dynamic-db')->name('radios.export');
    Route::get('radios/getRadiosInfo', [RadioController::class, 'radiosInfo'])->middleware('set-dynamic-db')->name('radios.getRadiosInfo');

    Route::get('/edit-page/{id}', [PageController::class, 'edit'])->name('edit.page');
    Route::post('/update-page/{id}', [PageController::class, 'update'])->name('update.page');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/actions', [UserPermissionController::class, 'getActions']);
    Route::get('/pages', [UserPermissionController::class, 'getPages']);

    Route::get('users/{user}/permissions', [UserPermissionController::class, 'getPermissions']);
    Route::get('users/{user}/resetSenha', [UserController::class, 'getPassword']);
    Route::get('users/{user}/permissionsSelect', [UserPermissionController::class, 'getPermissionsSelect']);
    Route::get('users/trocar-senha', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('users/trocar-senha', [UserController::class, 'updatePassword'])->name('senha.update');
    Route::post('users/{user}/permissions', [UserPermissionController::class, 'updatePermissions']);
    Route::post('/update-region-connection', [AccessDataController::class, 'updateRegionConnection'])->name('update.region.connection');
    Route::resource('accessData', AccessDataController::class);
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

   /* Route::post('/set-user-session/regiao', function (Request $request) {
        // Use o objeto $request para acessar a sessãod        
        session()->put('regiao', $request->input('regiao'));
        
        return response()->json(['success' => true]);
    })->name('set.user.session.regiao');*/

   


    Route::prefix('statistics')->middleware('set-dynamic-db')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/access', [StatisticsController::class, 'accessStatistics'])->name('statistics.access');
        Route::get('/storage', [StatisticsController::class, 'storageUsage'])->name('statistics.storage');
    });

    Route::prefix('forms')->middleware('check-page-access:ler')->group(function () {
        Route::get('/', [FormController::class, 'index'])->name('forms.index');
        Route::get('/{id}', [FormController::class, 'show'])->name('forms.show');      

        Route::post('/store', [FormController::class, 'store'])->middleware('check-page-access:gravar')->name('forms.store');
        Route::put('/{id}', [FormController::class, 'update'])->middleware('check-page-access:atualizar')->name('forms.update');
        Route::delete('/{id}', [FormController::class, 'destroy'])->middleware('check-page-access:excluir')->name('forms.destroy');
        
    });



    Route::prefix('radios')->middleware('check-page-access:ler','set-dynamic-db')->group(function () {        
        Route::get('/', [RadioController::class, 'index'])->name('radios.index');  
        Route::get('/indexSelect', [RadioController::class, 'indexSelect'])->name('radios.indexSelect');   
        Route::get('/track', [RadioController::class, 'track'])->name('radios.track');  
        Route::get('/basetrack', [RadioController::class, 'basetrack'])->name('radios.basetrack'); 
        Route::get('/{id}/machistory', [RadioController::class, 'macHistory'])->name('radios.machistory');   
        Route::get('/mapaRadio', [RadioController::class, 'getGeoRadio'])->name('radios.getGeoRadio');
        Route::get('/RelatoriosRadios', [RadioController::class, 'radioRelatorio'])->name('radios.RelatoriosRadios');
        Route::get('/rastrearRadiosUso', [RadioController::class, 'rastrearRadiosUso'])->name('radios.rastrearRadiosUso');
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
        Route::get('/controladora', [AccessDataController::class, 'index'])->name('controladora.indexControladora');
        Route::get('/{id}', [AccessDataController::class, 'show'])->name('controladora.show');
        Route::post('/', [AccessDataController::class, 'store'])->middleware('check-page-access:gravar')->name('controladora.store');  
        Route::put('/{id}', [AccessDataController::class, 'update'])->middleware('check-page-access:atualizar')->name('controladora.update');       
        Route::delete('/{id}', [AccessDataController::class, 'destroy'])->middleware('check-page-access:excluir')->name('controladora.destroy');
    });
    
    Route::prefix('database')->middleware('check-page-access:ler')->group(function () {        
        Route::get('/', [AccessDataController::class, 'indexDatabases'])->name('database.index');
        Route::get('/showStatistics', [AccessDataController::class, 'showStatistics'])->name('database.showStatistics');
        Route::get('/{id}', [AccessDataController::class, 'show'])->name('database.show');
        Route::post('/', [AccessDataController::class, 'store'])->middleware('check-page-access:gravar')->name('database.store');  
        Route::put('/{id}', [AccessDataController::class, 'update'])->middleware('check-page-access:atualizar')->name('database.update');       
        Route::delete('/{id}', [AccessDataController::class, 'destroy'])->middleware('check-page-access:excluir')->name('database.destroy');
    });

    Route::prefix('radius')->middleware('check-page-access:ler')->group(function () {        
        Route::get('/', [AccessDataController::class, 'indexRadius'])->name('radius.index');
        Route::get('/showStatistics', [AccessDataController::class, 'showStatistics'])->name('radius.showStatistics');
        Route::get('/{id}', [AccessDataController::class, 'show'])->name('radius.show');
        Route::post('/', [AccessDataController::class, 'store'])->middleware('check-page-access:gravar')->name('radius.store');  
        Route::put('/{id}', [AccessDataController::class, 'update'])->middleware('check-page-access:atualizar')->name('radius.update');       
        Route::delete('/{id}', [AccessDataController::class, 'destroy'])->middleware('check-page-access:excluir')->name('radius.destroy');
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
    Route::prefix('usuarios')->middleware('check-page-access:ler','set-dynamic-db')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->middleware('set-dynamic-db')->name('usuarios.index');
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
    Route::delete('/cards/{id}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::resource('cards', CardController::class);
    Route::resource('notes', NoteController::class);


});

require __DIR__.'/auth.php';
