<?php

namespace App\Services\Gallery;

use App\Common\DTO\FileDTO;
use App\Repository\Gallery\GalleryRepository;
use App\Utils\Upload;
use App\Repository\Gallery\DTO\SaveFileDTO;
use App\Services\Gallery\DTO\GalleryServiceUploadDTO;
use Illuminate\Support\Facades\Config;

class GalleryService
{
    private string $path = 'images';
    private Upload $uploader;
    public function __construct(private readonly GalleryRepository $repository)
    {
        $this->uploader = Upload::factory($this->path);
    }

    public function service(GalleryServiceUploadDTO $uploadDTO)
    {
        $file = $this->uploadFile($uploadDTO->file);
        $path = $this->path . '/' . $file;
        $saveFileInfo = new SaveFileDTO();
        $saveFileInfo->fill([
            'title' => $uploadDTO->title,
            'description' => $uploadDTO->description,
            'path' => $path
        ]);
        return $this->repository->savePicture($saveFileInfo);
    }

    private function uploadFile(FileDTO $file): ?string
    {
        $this->uploader->file([
            'error' => $file->error,
            'name' => $file->name,
            'type' => $file->type,
            'tmp_name' => $file->tmp_name,
            'size' => $file->size,
        ]);
        $this->uploader->set_max_file_size(5);
        $this->uploader->set_allowed_mime_types([
            'image/png',
            'image/jpg',
            'image/jpeg',
        ]);

        $results =  $this->uploader->upload();

        $file = "{$results['filename']}.{$results['type']}";

        if (count($results['errors']) > 0) {
            $errors = '';

            foreach ($results['errors'] as $error) {
                $errors .= $error . '<br>';
            }

            throw new \Exception($errors);
        }

        return $file;
    }
}
