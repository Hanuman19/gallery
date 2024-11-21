<?php

namespace App\Http\Response\Gallery;

use App\Http\Response\BaseResponse;
use Illuminate\Database\Eloquent\Collection;

class IndexResponse extends BaseResponse
{
    public Collection $items;
}
