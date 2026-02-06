<?php

namespace App\Http\Responses;

use Illuminate\Pagination\AbstractPaginator;

class PaginatedResponse extends BaseResponse
{
    public function __construct(AbstractPaginator $paginator, ?string $message = null)
    {
        $this->withMeta([
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ]);

        $this->withLinks([
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ]);

        parent::__construct($paginator->items(), $message);
    }
}
