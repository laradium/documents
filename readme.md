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

Before you can start generating documents, you need to add specific columns to your model. You can use `documentColumns($blueprint)` function
for this, or you can add them manually. 

In the end, your migration should look like this

```PHP
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');

            $table->string('other_field')->nullable();

            documentColumns($table); //Or DocumentService::columns($table)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
```

To generate a document you need to implement `DocumentableInterface` in the model, for which the document will be generated. 

After that you need to add the `getPlaceholders()` method and the relationship to the `Document` model, or you can simply add the `Documentable` trait
which will do these things for you

By default, all fillable and appended model attributes will be available as placeholders, but you can easily change this by creating your own `getPlaceholders()`
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

You can also add `$set->downloadDocument()` field, which will create a "Download" button for existing models.

## Configuration

By default, revisions are enabled and the default model directory is already set to `app/Models` but you can easily change
this in the `laradium-documents.php` config file.

You can also add custom models, which are in different directory and uses different namespace.

To add custom placeholders, simply add them to the `custom_placeholders` array.
You can add simple values, or functions which returns the necessary value. This way you can create reusable snippets

## Events

This module uses the default Laravel event system, so you can easily attach listeners to events.

- `Laradium\Laradium\Documents\Events\DocumentCreated` - Triggered when a new document template is created
- `Laradium\Laradium\Documents\Events\DocumentUpdated` - Triggered when a document template is updated
- `Laradium\Laradium\Documents\Events\DocumentGenerated` - Triggered when a document is generated (downloaded for the first time)
