<?php

namespace App\Http\Resources\Department;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DepartmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

           'items' => DepartmentResource::collection($this->collection),

           'pagination' => [
               'currentPage' => $this->currentPage(),
               'lastPage'    => $this->lastPage(),
               'perPage'     => $this->perPage(),
               'total'       => $this->total(),
            ],
        ];
    }
}
