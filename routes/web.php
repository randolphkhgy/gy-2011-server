<?php

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

Route::get('/lotteries', 'LotteryController@index');
Route::get('/lotteries/shuzi', 'LotteryController@shuzi');
Route::get('/lotteries/shuzivn', 'LotteryController@shuzivn');
Route::get('/lotteries/elevenfive', 'LotteryController@elevenfive');
Route::get('/lottery/{id}', 'LotteryController@show');

Route::post('/issue/generate', 'Issue\\IssueGenerateController@process');
Route::post('/issue/drawdate', 'Issue\\IssueDrawerController@drawDate');
