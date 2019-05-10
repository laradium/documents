<?php

namespace Laradium\Laradium\Documents\Services;

use Illuminate\Database\Schema\Blueprint;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;
use Throwable;

class DocumentService
{
    /**
     * @var ParserService
     */
    private $parser;

    /**
     * DocumentService constructor.
     */
    public function __construct()
    {
        $this->parser = new ParserService();
    }

    /**
     * @return ParserService
     */
    public function getParser(): ParserService
    {
        return $this->parser;
    }

    /**
     * @param DocumentableInterface $documentable
     * @return string
     */
    public function render(DocumentableInterface $documentable): string
    {
        return $this->getParser()->render($documentable);
    }

    /**
     * @param DocumentableInterface $documentable
     * @return mixed
     * @throws Throwable
     */
    public function download(DocumentableInterface $documentable)
    {
        $pdf = app('dompdf.wrapper');

        $pdf->loadHTML(view(config('laradium-documents.pdf_view'), [
            'content' => $documentable->content ?: $this->render($documentable)
        ])->render());

        return $pdf->stream();
    }

    /**
     * @param Blueprint $blueprint
     */
    public static function columns(Blueprint $blueprint): void
    {
        $blueprint->integer('document_id')->unsigned();
        $blueprint->longText('content')->nullable();
    }
}
