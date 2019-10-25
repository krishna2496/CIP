<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class Helpers
{
    /**
     * Pagination transform
     *
     * @param object $data
     * @param array $requestString
     * @param string $url
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginationTransform(object $data, array $requestString, string $url): LengthAwarePaginator
    {
        $paginatedData = new LengthAwarePaginator(
            $data->getCollection(),
            $data->total(),
            $data->perPage(),
            $data->currentPage(),
            [
                'path' => $url.'?'.http_build_query($requestString),
                'query' => [
                    'page' => $data->currentPage()
                ]
            ]
        );
        return $paginatedData;
    }
}
