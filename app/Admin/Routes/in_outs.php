<?php
$router->group(['prefix' => 'in_outs'], function ($router) {
    $router->get('/', 'AdminInOutsController@index')->name('in_outs.index');
    $router->post('/save', 'AdminInOutsController@save')->name('in_outs.save');
    $router->post('/delete', 'AdminInOutsController@delete')->name('in_outs.delete');
});
