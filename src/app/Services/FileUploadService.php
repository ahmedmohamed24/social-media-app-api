<?php

namespace App\Services;

use App\Models\Post;
use App\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileUploadService
{
    public function upload()
    {
        $temporaryUpload = TemporaryUpload::create();
        $temporaryUpload->addMultipleMediaFromRequest(['files'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection();
            })
        ;

        return $temporaryUpload->media;
    }

    public function savePostFiles($media, Post $post)
    {
        Media::query()->findMany($media)
            ->each(function (Media $mediaFile) use ($post) {
                $mediaFile->move($post, 'posts');
                $mediaFile->model()->delete();
            })
            ;
    }
}
