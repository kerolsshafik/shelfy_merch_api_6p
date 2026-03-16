<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait PaginationHandling
{
    protected function getPaginationData($data)
    {
        return [
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'total_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total_items' => $data->total(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
            'first_page_url' => $data->url(1),
            'last_page_url' => $data->url($data->lastPage()),
            'path' => $data->path(),
        ];
    }
}
