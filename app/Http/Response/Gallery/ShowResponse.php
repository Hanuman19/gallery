<?php

namespace App\Http\Response\Gallery;

use App\Http\Response\BaseResponse;
use App\Models\Gallery;

class ShowResponse extends BaseResponse
{
    public Gallery $item;
}
