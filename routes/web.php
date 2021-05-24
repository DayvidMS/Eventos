<?php

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
use App\Http\Controllers\EventController;

//Busca os dados do banco
Route::get('/', [EventController::class,'index']);
//chama a view events.create que leva ao formulario de cadastro de eventos, o usuario só consegue acessar caso esteja logado, caso não esteja vai ser redirecionado para a tela de login
Route::get('/events/create', [EventController::class,'create'])->middleware('auth');
//faz o insert do formulario de criação de eventos via post.
Route::post('/events', [EventController::class,'store']);
//chama a view events para mostrar um dado do especifico de um evento
Route::get('/events/{id}', [EventController::class,'show']);
//Chama a rota dashboard para retornar os dados dos eventos do usuario
Route::get('/dashboard',[EventController::class, 'dashboard'])->middleware('auth');
//Rota que chama a função para deletar o dado
Route::delete('/events/{id}',[EventController::class, 'destroy'])->middleware('auth');
//Rota que chama a página para edição de dados
Route::get('events/edit/{id}',[EventController::class,'edit'])->middleware('auth');
//recebe as mudanças do formulario e faz o update
Route::put('events/update/{id}',[EventController::class,'update'])->middleware('auth');
//Recebe o a confirmação de participação no Evento
Route::post('/events/join/{id}',[EventController::class,'joinEvent'])->middleware('auth');
//Rota para chamar a função para sair do evento
Route::delete('/events/leave/{id}',[EventController::class,'leaveEvent'])->middleware('auth');
Route::get('/contact', function () {
    return view('contact');
});


