<?php

namespace App\Repository\Gallery;

use App\Models\Gallery;
use App\Repository\Gallery\DTO\SaveFileDTO;
use Illuminate\Database\Eloquent\Collection;


class GalleryRepository
{
    public function getAllPictures(): Collection
    {
        return Gallery::all();
    }

    public function savePicture(SaveFileDTO $saveDTO): Gallery
    {
        $galery = new Gallery();
        $galery->title = $saveDTO->title;
        $galery->description = $saveDTO->description;
        $galery->path = $saveDTO->path;
        $galery->save();
        return $galery;
    }

    public function getPictureById(int $id): Gallery
    {
        return Gallery::where(['id' => $id])->first();
    }
}

