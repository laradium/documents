<?php

use Laradium\Laradium\Documents\Services\ParserService;

return [
    //Toggle document revisions.
    'revisions'           => true,

    //View which will be used for PDF generation
    'pdf_view'            => 'laradium-documents::pdf',

    //The font family, which should be used in PDF generation
    'font_family'         => 'DejaVu Sans, serif',

    //The path that will be checked for documentable models
    'model_path'          => base_path('app/Models'),

    //The namespace that models in the path use
    'model_namespace'     => 'App\\Models',

    //The service, which is responsible for document generation
    'parser_service'      => ParserService::class,

    //Models that are not in the path, but still are documentable
    'models'              => [],

    //Determines, if documents should automatically render when model is created
    'auto_render'         => true,

    //Custom placeholders which will be able to all documentable models
    'custom_placeholders' => [
        'app.name' => config('app.name')
    ]
];
