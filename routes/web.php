<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminActivityLogController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;

// Rotas de verificação de email
Route::get('/email/verify', [EmailVerificationController::class, 'showVerificationForm'])
    ->name('verification.verify.form');

Route::post('/email/verify', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

Route::get('/email/verify/resend', [EmailVerificationController::class, 'resend'])
    ->name('verification.resend');

// Página inicial
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas de autenticação personalizadas
Route::middleware('guest')->group(function () {
    // Registro
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
    
    // Verificação de email
    Route::get('/verify-email', [RegisterController::class, 'showVerificationForm'])->name('verification.notice');
    Route::post('/verify-email', [RegisterController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/resend-verification', [RegisterController::class, 'resendVerificationCode'])->name('verification.resend');
});

// Rotas públicas
Route::resource('games', GameController::class)->only(['index', 'show']);
Route::get('/games/{game}/download', [GameController::class, 'download'])->name('games.download');
Route::post('/games/{game}/rate', [GameController::class, 'rate'])->name('games.rate');
Route::get('/games/{game}/pdf', [GameController::class, 'generatePdf'])->name('games.pdf');
Route::resource('news', NewsController::class)->only(['index', 'show']);
Route::get('/help', [HelpController::class, 'index'])->name('help.index');

// Rotas de perfil
Route::middleware(['auth', \App\Http\Middleware\CheckBlockedUser::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Área administrativa (requer autenticação, verificação de email E admin)
Route::prefix('admin')->middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gerenciamento de Jogos
    Route::prefix('games')->name('games.')->group(function () {
        // Listagem admin
        Route::get('/', [GameController::class, 'adminIndex'])->name('index');
        
        // CRUD
        Route::get('/create', [GameController::class, 'create'])->name('create');
        Route::post('/', [GameController::class, 'store'])->name('store');
        Route::get('/{game}/edit', [GameController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{game}', [GameController::class, 'update'])->name('update');
        
        // Ações específicas
        Route::delete('/{game}', [GameController::class, 'destroy'])->name('destroy');
        Route::post('/{game}/restore', [GameController::class, 'restore'])->name('restore');
        Route::post('/{game}/toggle-featured', [GameController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::get('/{game}/admin-download', [GameController::class, 'adminDownload'])->name('download');
    });
    
    // Gerenciamento de Notícias
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'adminIndex'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
        Route::post('/{news}/restore', [NewsController::class, 'restore'])->name('restore');
        Route::post('/{news}/toggle-featured', [NewsController::class, 'toggleFeatured'])->name('toggle-featured');
    });
    
    // Gerenciamento de Usuários
    Route::prefix('users')->name('users.')->group(function () {
        // Listagem
        Route::get('/', [UserController::class, 'index'])->name('index');
        
        // Criação
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        
        // Edição
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{user}', [UserController::class, 'update'])->name('update');
        
        // Ações administrativas
        Route::post('/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('toggle-admin');
        Route::post('/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('toggle-block');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore');
    });
    
    // Conteúdo de Ajuda
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'adminIndex'])->name('index');
        Route::get('/edit', [HelpController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/update', [HelpController::class, 'update'])->name('update');
    });
    
    // Logs de Atividade - CORRIGIDO
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [AdminActivityLogController::class, 'index'])->name('index');
        Route::get('/filter', [AdminActivityLogController::class, 'filter'])->name('filter');
        Route::get('/{log}', [AdminActivityLogController::class, 'show'])->name('show');
    });
});

// Rota dashboard padrão do Laravel (redireciona para home)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// Rotas de autenticação padrão do Laravel (mantenha para login/logout)
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});