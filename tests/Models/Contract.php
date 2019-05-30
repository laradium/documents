<?php

namespace Laradium\Laradium\Documents\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;
use Laradium\Laradium\Documents\Traits\Documentable;

class Contract extends Model implements DocumentableInterface
{
    use Documentable;

    /**
     * @var array
     */
    protected $fillable = [
        'document_id',
        'content',
        'key'
    ];
}
