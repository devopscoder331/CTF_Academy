<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailingListController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\MailingListMiddleware;
use Illuminate\Support\Facades\DB;
use UniSharp\LaravelFilemanager\Controllers\UploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/management/dashboard', function() {
    $user = request()->attributes->get('user');
    $mailing = request()->attributes->get('mailing');
    return view('dashboard', ['user' => $user, 'mailing' => $mailing]);
})->middleware([AuthMiddleware::class, MailingListMiddleware::class]);

Route::get('/management/info', function() {
    if(App::environment() !== "production") {
      $user = request()->attributes->get('user');
      return view('info', ['user' => $user]);
    }
    return redirect('/management/dashboard');
})->middleware([AuthMiddleware::class]);

Route::get('/management/profile', function() {
    $user = request()->attributes->get('user');
    return view('profile', ['user' => $user]);
})->middleware([AuthMiddleware::class]);

Route::get('/login', function() {
    return view('login');
});

Route::post('/mailing', [MailingListController::class, 'store']);

Route::get('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
});

Route::post('/upload', function (Request $request) {
    $response = app(UploadController::class)->upload($request);
    $responseBody = $response->getContent();
    $searchString = 'error';
    if (strpos($responseBody, $searchString) === false) {
      $responseArray = json_decode($responseBody, false);
      $uploadedURL = $responseArray->uploaded;
      $filename = basename($uploadedURL);
      $id = $request->session()->get('user_id');
      $user = User::find($id);
      $user->profile_picture = $filename;
      $user->save();
    }
    return $response;
})->name('unisharp.lfm.upload')->middleware([AuthMiddleware::class]);

Route::post('/login', function (Request $request) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = $_POST['remember'];

    if($remember == 'False') {
        $keep_loggedin = False;
    } elseif ($remember == 'True') {
        $keep_loggedin = True;
    }

    if($keep_loggedin !== False) {
    // TODO: Keep user logged in if he selects "Remember Me?"
    }

    $user = User::where('email', $email)->first();

    if ($user && Hash::check($password, $user->password)) {
        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);
        return redirect('/management/dashboard');
    }

    return redirect('/login?error=Invalid credentials.');
});
