<?php

namespace App\Http\Controllers\Api;

use App\Common\DTO\FileDTO;
use App\Http\Controllers\Controller;
use App\Http\Request\Gallery\UploadRequest;
use App\Http\Response\Gallery\IndexResponse;
use App\Http\Response\Gallery\ShowResponse;
use App\Http\Response\Gallery\UploadResponse;
use App\Repository\Gallery\GalleryRepository;
use App\Services\Gallery\DTO\GalleryServiceUploadDTO;
use App\Services\Gallery\GalleryService;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $service)
    {

    }
    public function index(): array
    {
        $repository = new GalleryRepository();
        $response = new IndexResponse();
        $response->items = $repository->getAllPictures();
        $response->success = true;
        return $response->toArray();
    }

    public function upload(UploadRequest $request): array
    {
        $response = new UploadResponse();

        $file = new FileDTO();
        $file->error = $request->image->getError();
        $file->name = $request->image->getClientOriginalName();
        $file->type = $request->image->getMimeType();
        $file->tmp_name = $request->image->getPathname();
        $file->size = $request->image->getSize();

        $upload = new GalleryServiceUploadDTO();
        $upload->file = $file;
        $upload->title = $request->title;
        $upload->description = $request->description;

        $result = $this->service->service($upload);

        $response->success = true;
        $response->item = $result->toArray();
        return $response->toArray();
    }
    public function show(int $id): array
    {
        $repository = new GalleryRepository();
        $response = new ShowResponse();
        $response->item = $repository->getPictureById($id);
        $response->success = true;
        return $response->toArray();
    }
}
