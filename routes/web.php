<?php


Route::group(['middleware' => 'auth'], function () {
    Route::get('budgets/create', 'BudgetsController@create');
    Route::post('budgets', 'BudgetsController@store');
    Route::get('budgets/{id}/edit', 'BudgetsController@edit');
    Route::patch('budgets/{id}', 'BudgetsController@update');
});

Route::get('/login', 'LoginController@create')->name('login');