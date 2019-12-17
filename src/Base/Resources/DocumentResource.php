<?php

namespace Laradium\Laradium\Documents\Base\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Documents\Events\DocumentCreated;
use Laradium\Laradium\Documents\Events\DocumentUpdated;
use Laradium\Laradium\Documents\Interfaces\DocumentableInterface;
use Laradium\Laradium\Documents\Models\Document;
use Laradium\Laradium\Documents\Services\DocumentService;
use ReflectionException;
use Throwable;

class DocumentResource extends AbstractResource
{
    /**
     * @var string
     */
    protected $resource = Document::class;

    /**
     * @var DocumentService
     */
    protected $service;

    /**
     * DocumentResource constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->service = new DocumentService();
    }

    /**
     * @return Resource
     */
    public function resource()
    {
        $this->event('afterSave', function ($model, $request) {
            $document = $this->getModel()->find($request->get('previous_document_id', 0));

            if ($document) {
                $document->update([
                    'type' => $this->getModel()::TYPE_REVISION
                ]);

                return event(new DocumentUpdated($model));
            }

            return event(new DocumentCreated($model));
        });

        return (new Resource)->make(function (FieldSet $set) {
            $set->block(9)->fields(function (FieldSet $set) {
                $set->text('title')->rules('required|min:3')->attr([
                    'id' => 'title'
                ]);

                $set->documentEditor('content')->rules('required|min:3')->attr([
                    'id' => 'content',
                ]);
            });

            $set->block(3)->fields(function (FieldSet $set) {
                if (config('laradium-documents.revisions', true)) {
                    $document = $this->getModel()->find(request()->route()->document ?? 0);

                    $revisions = $document ? $document->revisions()->get() : [];

                    $set->select('revert_to')->options($this->getRevisionOptions($revisions))->attr([
                        'id' => $document ? 'revert-to' : ''
                    ]);

                    $set->hidden('revision_json')->modifyValue(function () use ($revisions) {
                        return json_encode($revisions);
                    });
                }

                $set->customContent(function () {
                    return view('laradium-documents::_partials.placeholders', [
                        'placeHolders' => $this->service->getPlaceholders()
                    ])->render();
                });

                $set->saveButtons()->withoutLanguageSelect();
            });
        })->js([
            asset('laradium/assets/js/documents/documents.js')
        ]);
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('id', '#ID');
            $column->add('title', 'Title');
            $column->add('created_at', 'Created At');
        })->where(function (Builder $query) {
            return $query->current();
        });
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->save($request, $id);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function store(Request $request): JsonResponse
    {
        return $this->save($request);
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     * @throws Throwable
     */
    public function download($type, $id)
    {
        $documentable = $this->findDocumentableByType($type, $id);

        abort_unless((bool)$documentable, 404);

        return $this->service->pdf($documentable)->download();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ReflectionException
     */
    private function save(Request $request, $id = 0): JsonResponse
    {
        $additionalData = [
            'user_id'  => auth()->id(),
            'key'      => sha1(uniqid('', true)),
            'revision' => 1,
            'type'     => Document::TYPE_CURRENT
        ];

        $document = $this->getModel()->find($id);

        if ($document) {
            $additionalData['previous_document_id'] = $document->id;
            $additionalData['key'] = $document->key;
            $additionalData['revision'] = $document->revision + 1;
        }

        $request->merge($additionalData);

        return parent::store($request);
    }

    /**
     * @param $type
     * @param $id
     * @return null|DocumentableInterface
     */
    private function findDocumentableByType($type, $id): ?DocumentableInterface
    {
        foreach ($this->service->getParser()->getDocumentableModels() as $model) {
            if (strtolower(class_basename($model)) === $type) {
                return app($model)->find($id);
            }
        }

        return null;
    }

    /**
     * @param $revisions
     * @return array
     */
    private function getRevisionOptions($revisions): array
    {
        if (!$revisions) {
            return [
                0 => 'No history available'
            ];
        }

        $options = [
            0 => 'Please select...'
        ];

        foreach ($revisions as $revision) {
            $options[$revision->id] = $revision->title . ' - ' . $revision->created_at;
        }

        return $options;
    }
}
