# Document module for Laradium CMS
This module adds document template storing and document generation from models.

## Installation

## For local use

1. Add this to your project repositories list in `composer.json` file

```
"repositories": [
    {
      "type": "path",
      "url": "../packages/laradium"
    },
    {
      "type": "path",
      "url": "../packages/laradium-documents"
    }
  ]
```

Directory structure should look like this

```
-Project
-packages
--laradium
--laradium-documents
```

## For global use

```
"repositories": [
        {
            "type": "git",
            "url": "https://github.com/laradium/laradium.git"
        },
        {
            "type": "git",
            "url": "https://github.com/laradium/laradium-documents.git"
        }
    ]
```

2. ```composer require laradium/laradium-documents dev-master```
3. ```php artisan vendor:publish --tag=laradium-documents```
4. Configure `config/laradium-documents.php` file with your preferences

## Usage

This module will add a new section in the admin panel called "Documents". This is the place where all the document
templates are stored.

To generate a document you need to implement `DocumentableInterface` in the model, for which the document will be generated. 

After that you need to add the `getPlaceholders()` method and the relationship to the `Document` model, or you can simply add the `Documentable` trait
which will do these things for you

By default, all fillable model attributes will be available as placeholders, but you can easily change this by creating your own `getPlaceholders()`
method in the model

In the end, your model should look like this:
```PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;
use Laradium\Laradium\Documents\Traits\Documentable;

class Invoice extends Model implements DocumentableInterface
{
    use Documentable;

    protected $fillable = [
        'other_field',
        'document_id',
        'content'
    ];
}
```

After that create a Laradium resource for the model, and add the document field using `$set->document('document_id')`.
This will create a select with available document templates.
