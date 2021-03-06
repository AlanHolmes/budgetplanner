<?php


Route::group(['middleware' => 'auth'], function () {
    Route::get('budgets', 'BudgetsController@index');
    Route::get('budgets/create', 'BudgetsController@create');
    Route::post('budgets', 'BudgetsController@store');
    Route::get('budgets/{id}/edit', 'BudgetsController@edit');
    Route::patch('budgets/{id}', 'BudgetsController@update');

    Route::get('transactions/create', 'TransactionsController@create');
    Route::post('transactions', 'TransactionsController@store');
});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');