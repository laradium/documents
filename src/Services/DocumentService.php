<?php

namespace Laradium\Laradium\Documents\Services;

use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Laradium\Laradium\Documents\Exceptions\MissingRelationException;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;
use Throwable;

class DocumentService
{
    /**
     * @var ParserService
     */
    private $parser;

    /**
     * @var Application|PDF
     */
    private $pdf;

    /**
     * DocumentService constructor.
     */
    public function __construct()
    {
        $parserService = config('laradium-documents.parser_service', ParserService::class);

        $this->parser = new $parserService();
        $this->pdf = app('dompdf.wrapper');
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
     * @throws MissingRelationException
     */
    public function render(DocumentableInterface $documentable): string
    {
        return $this->getParser()->render($documentable);
    }

    /**
     * @return array
     */
    public function getPlaceholders(): array
    {
        return $this->getParser()->getPlaceholders();
    }

    /**
     * @param DocumentableInterface $documentable
     * @return DocumentService
     * @throws MissingRelationException
     * @throws Throwable
     */
    public function pdf(DocumentableInterface $documentable): self
    {
        $this->pdf = $this->pdf->loadHTML(view(config('laradium-documents.pdf_view'), [
            'document' => $documentable,
            'content'  => $documentable->getContent() ?: $this->render($documentable)
        ])->render());

        return $this;
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    public function stream()
    {
        return $this->pdf->stream();
    }

    /**
     * @param null $name
     * @return Response
     */
    public function download($name = null): Response
    {
        if (!$name) {
            $name = time();
        }

        return $this->pdf->download($name . '.pdf');
    }

    /**
     * @param null $path
     * @return $this
     */
    public function save($path = null): self
    {
        if (!$path) {
            $path = storage_path('laradium/documents/' . time() . '.pdf');
        }

        $this->pdf->save($path);

        return $this;
    }

    /**
     * @param Blueprint $blueprint
     * @param bool $withForeign
     * @return void
     */
    public static function columns(Blueprint $blueprint, $withForeign = false): void
    {
        $blueprint->integer('document_id')->unsigned();

        if ($withForeign) {
            $blueprint->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        }

        $blueprint->longText('content')->nullable();
    }
}
