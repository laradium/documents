<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'namespace'  => 'Admin',
    'middleware' => ['web'],
], function () {
    Route::group(['middleware' => 'laradium'], function () {
        Route::get('documents/{type}/{id}/download', [
            'uses' => '\Laradium\Laradium\Documents\Base\Resources\DocumentResource@download',
            'as'   => 'documents.download'
        ]);
    });
});
