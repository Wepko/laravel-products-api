<?php

// app/Http/Responses/PaginatedApiResponse.php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedApiResponse extends ApiResponse
{
    public function __construct(
        JsonResource|array   $data,
        LengthAwarePaginator $paginator,
        string               $message = 'Success',
        int                  $status = 200
    )
    {
        parent::__construct($data, $message, $status);

        $this->addPaginationMeta($paginator);
    }

    private function addPaginationMeta(LengthAwarePaginator $paginator): void
    {
        $this->meta([
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }
}
