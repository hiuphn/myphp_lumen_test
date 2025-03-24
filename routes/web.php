<?php


$router->get('/', function () use ($router) {
    return $router->app->version();
});

    $router->get('/users', 'UserController@index');
    $router->post('/users', 'UserController@addUser'); 
    $router->get('/users/{id}', 'UserController@showUser'); 
    $router->put('/users/{id}', 'UserController@editUser'); 
    $router->delete('/users/{id}', 'UserController@destroyUser');
// $router->group(['middleware' => ['auth', 'role:admin']], function () use ($router) {
//     $router->get('/users', 'UserController@index');
//     $router->post('/users', 'UserController@addUser'); 
//     $router->get('/users/{id}', 'UserController@showUser'); 
//     $router->put('/users/{id}', 'UserController@editUser'); 
//     $router->delete('/users/{id}', 'UserController@destroyUser'); 
// });

// $router->get('/', function () {
//     return file_get_contents(__DIR__ . '/../public/vue-app/dist/index.html');
// });

$router->group(['prefix' => 'api/vat-invoices'], function () use ($router) {
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('/', 'VatInvoiceController@add');  
        $router->group(['middleware' => ['role:admin']], function () use ($router) {
            $router->get('/', 'VatInvoiceController@index');  
            $router->get('/{id}', 'VatInvoiceController@show');  
            $router->put('/{id}', 'VatInvoiceController@update');  
            $router->put('/{updateId}/approve-or-reject', 'VatInvoiceController@approveOrReject');
            $router->delete('/{id}', 'VatInvoiceController@destroy');
        });
    }); 
    
});

$router->post('/auth/register', 'AuthController@register'); 
$router->post('/auth/login', 'AuthController@login');

$router->group(['middleware' => ['auth', 'role:admin']], function () use ($router) {
    $router->post('/assign-admin/{userId}', 'AdminController@assignAdminRole');
});

// $router->group(['middleware' => ['auth', 'role:admin, user']], function () use ($router) {
//     $router->post('/auth/register', 'AuthController@register');
// });

$router->group(['prefix' => 'roles'], function () use ($router) {
    $router->get('/', 'RoleController@index');         
    $router->post('/', 'RoleController@store');       
    $router->get('/{id}', 'RoleController@show');    
    $router->put('/{id}', 'RoleController@update');   
    $router->delete('/{id}', 'RoleController@destroy'); 
});

$router->group(['prefix' => 'api'], function () use ($router) {
    //Song
    $router->get('/songs/trending-songs', 'SongController@getTrendingSongs');
    $router->get('/songs', 'SongController@index');
    //Album
    $router->get('/albums', 'AlbumController@index');  
    $router->get('/albums/popular', 'AlbumController@getPopularAlbums');
    $router->get('/albums/{id}', 'AlbumController@show');
    //Playlist
    
    $router->get('/search', 'SearchController@search');

    
    $router->group(['middleware' => 'auth'], function () use ($router) {
        //Playlist
        $router->get('/playlists', 'PlaylistController@index');   
        $router->post('/playlists', 'PlaylistController@store');  
        $router->get('/playlists/{id}', 'PlaylistController@show'); 
        $router->put('/playlists/{id}', 'PlaylistController@update'); 
        $router->delete('/playlists/{id}', 'PlaylistController@destroy'); 
        $router->post('/playlists/{playlistId}/add-song', 'PlaylistController@addSongToPlaylist');  
        $router->delete('/playlists/{playlistId}/remove-song', 'PlaylistController@removeSongToPlaylist');
        
        $router->group(['middleware' => 'role:admin'], function () use ($router) { 
            //Album 
            $router->post('/albums', 'AlbumController@store');  
            $router->put('/albums/{id}', 'AlbumController@update');  
            $router->delete('/albums/{id}', 'AlbumController@destroy');
            $router->post('/albums/{albumId}/add-song', 'AlbumController@addSongToAlbum');
            $router->delete('/albums/{albumId}/remove-song', 'AlbumController@removeSongToAlbum');
            //Song
            $router->post('/songs', 'SongController@store');
            $router->get('/songs/{id}', 'SongController@show');
            $router->put('/songs/{id}', 'SongController@update');
            $router->delete('/songs/{id}', 'SongController@destroy');
        });
    });
});
