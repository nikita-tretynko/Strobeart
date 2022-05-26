<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Class ImageService
 *
 * @package App\Services
 */
class ImageService
{
    /**
     * Return base path
     *
     * @param string $path
     *
     * @return string
     */
    public function returnBasePath(string $path): string
    {
        return env('APP_URL') . '/storage/' . $path;
    }

    /**
     * Image base path to real path
     *
     * @param string $base_path
     *
     * @return string|null
     */
    public function returnRealPath(string $base_path): ?string
    {
        $path = str_replace(
            env('APP_URL') . '/storage',
            storage_path('app/public'),
            $base_path
        );

        if (!file_exists($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Save image to disk
     *
     * @param $image
     * @param string|null $path
     *
     * @return string
     * @throws Exception
     */
    public function saveImage($image, ?string $path): string
    {
        $save_path = $path ?? 'temp/image';

        try {
            Storage::disk('public')->put($save_path, $image);
        } catch (Exception $e) {
            throw new Exception('No save image! ERROR: ' . $e->getMessage(), 500);
        }

        return $this->returnBasePath($save_path);
    }

    public function saveImageResizedJob($file, $patch, $with = 530, $height = 580, $photo_name = null): string
    {
        try {
            list($with2, $height2) = getimagesize($file);
            $photo_name = $photo_name ?? $file->getClientOriginalName();
            $save_path = $patch . '/size' . $with . '-' . $height . '/' . 'mini-' . $photo_name ?? 'temp/image/' . $photo_name;
            $resizedImage = Image::make($file->getRealPath());
            if ($with2 >= $with || $height2 >= $height) {
                $resizedImage->fit($with, $height, function ($resizedImage) {
                    $resizedImage->aspectRatio();
                    $resizedImage->upsize();
                });
            }
            $resizedImage->stream();
            Storage::disk('public')->put($save_path, $resizedImage);

        } catch (Exception $e) {
            throw new Exception('No save image! ERROR: ' . $e->getMessage(), 500);
        }

        return $this->returnBasePath($save_path);
    }

    public function saveImageJob($file, $patch, $photo_name = null): string
    {
        try {
            $photo_name = $photo_name ?? $file->getClientOriginalName();
            $save_path = $patch . '/size-full' . '/';
            $file->storeAs('public/' . $save_path, $photo_name);
        } catch (Exception $e) {
            throw new Exception('No save image! ERROR: ' . $e->getMessage(), 500);
        }

        return $this->returnBasePath($save_path . $photo_name);
    }

    /**
     * Decode image from base 64
     *
     * @param string $image_string
     *
     * @return string
     */
    public function fromBase64(string $image_string): string
    {
        $tmp_array = explode(',', $image_string);
        $file = $tmp_array[array_key_last($tmp_array)];
        return base64_decode($file);
    }

    /**
     * Save image to disk (decode base 64)
     *
     * @param $image
     * @param string|null $path
     *
     * @return string
     * @throws Exception
     */
    public function saveImage64($image, ?string $path): string
    {
        $save_path = $path . '/' . $image['name'] ?? 'temp/image' . '/' . $image['name'];
        $image_file_content = $this->fromBase64($image['src']);

        return $this->saveImage($image_file_content, $save_path);
    }

    /**
     * Save images to disk
     *
     * @param array $images
     * @param string $path
     *
     * @return array
     * @throws Exception
     */
    public function saveImages(array $images, string $path, $image_job_id, $user): void
    {
        try {
            foreach ($images as $image) {
                $photo_name = Str::random(10) . '-' . $image->getClientOriginalName();
                \App\Models\Image::create([
                    'user_id' => $user->id,
                    'plan_id' => $user->plan->id,
                    'image_job_id' => $image_job_id,
                    'src' => $this->saveImageJob($image, $path, $photo_name),
                    'src_cropped' => $this->saveImageResizedJob($image, $path, 370, 354, $photo_name),
                ]);
            }
        } catch (Exception $e) {
            throw new Exception('No save images! ERROR: ' . $e->getMessage(), 500);
        }

    }

    /**
     * Delete image
     *
     * @param string $path (real path)
     *
     * @return void
     */
    public function deleteImage(string $path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function saveImageWork(UploadedFile $file, $options = ['path' => 'work']): array
    {
        $storeResult = Storage::disk('public')->put($options['path'], $file);
        $image_url = URL::to('/') . '/storage/' . $storeResult;

        return compact('image_url', 'storeResult');
    }

    /**
     * @param $image
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function convertImageForInstagram($image): string
    {
        $extension = pathinfo(storage_path($image), PATHINFO_EXTENSION);
        $name_img = '/temp-jpeg/image-'.mt_rand().'.jpeg';
        list($with, $height) = getimagesize(storage_path('app/public/' . $image));
        $new_with = $with;
        $new_height = $with;
        if ($height>$with){
            $new_height = intval(($with/4)+$with);
        }
        $image = Storage::disk('public')->get($image);

        if ($extension != 'jpeg') {
            $interventionImage = \Image::make($image)->resizeCanvas($new_with, $new_height)->stream("jpeg", 100);
        } else {
            $interventionImage = \Image::make($image)->resizeCanvas($new_with, $new_height);
        }
        Storage::disk('public')->put($name_img, $interventionImage);

        return $name_img;
    }
}
