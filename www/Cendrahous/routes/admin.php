<?php

Route::prefix('/admin')->group(function(){
    Route::get('/', 'Admin\DashboardController@getDashboard')->name('dashboard');

    // Modul Usuaris
    Route::get('users/{status}', 'Admin\UserController@getUsers')->name('user_list');
    Route::get('/user/{id}/edit', 'Admin\UserController@getUserEdit')->name('user_edit');
    Route::get('/user/{id}/banned', 'Admin\UserController@getUserBanned')->name('user_banned');
    //Exportar dades Usuaris en XML
    Route::get('/user/xml', 'XMLController@download_user')->name('usuaris_xml');

    // Modul Propietats
    Route::get('/properties', 'Admin\PropertyController@getHome')->name('properties');
    Route::get('/property/add', 'Admin\PropertyController@getPropertyAdd')->name('property_add');
    Route::get('/property/{id}/edit', 'Admin\PropertyController@getPropertyEdit')->name('property_edit');
    Route::post('/property/add', 'Admin\PropertyController@postPropertyAdd')->name('property_add');
    Route::post('/property/{id}/edit', 'Admin\PropertyController@postPropertyEdit')->name('property_edit');
    Route::get('/property/{id}/delete', 'Admin\PropertyController@getPropertyDelete')->name('property_delete');
    Route::post('/property/{id}/gallery/add', 'Admin\PropertyController@postPropertyGalleryAdd')->name('property_gallery_add');
    Route::get('/property/{id}/gallery/{gid}/delete', 'Admin\PropertyController@getPropertyGalleryDelete')->name('product_gallery_delete');
    //Exportar dades Propietats en XML
    Route::get('/property/xml', 'XMLController@download_property')->name('properties_xml');
    

    // Tipus de propietats (categories)
    Route::get('/types/{module}', 'Admin\TypesController@getHome')->name('types');
    Route::post('/type/add', 'Admin\TypesController@postTypeAdd')->name('type_add');
    Route::get('/type/{id}/edit', 'Admin\TypesController@getTypeEdit')->name('type_edit');
    Route::post('/type/{id}/edit', 'Admin\TypesController@postTypeEdit')->name('type_edit');
    Route::get('/type/{id}/delete', 'Admin\TypesController@getTypeDelete')->name('type_delete');
    //Exportar dades Tipus en XML
    Route::get('/type/xml', 'XMLController@download_type')->name('types_xml');

    //Api JSON
    Route::get('/users', 'ApiController@indexUsers')->name('api_users');
    Route::get('/properties/json', 'ApiController@indexProperties')->name('api_properties');

    Route::get('/BuscadorPropietats', 'BuscadorP@index')->name('BuscadorPropietats');
    Route::get('/BuscadorPropietats/action', 'BuscadorP@action')->name('BuscadorPropietats.action');
});