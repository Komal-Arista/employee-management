<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'items' => EmployeeResource::collection($this->collection),

            'pagination' => [
                'currentPage' => $this->currentPage(),
                'lastPage'    => $this->lastPage(),
                'perPage'     => $this->perPage(),
                'total'       => $this->total(),
             ],
         ];
    }
}
