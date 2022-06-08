<?php

namespace App\Jobs;

use App\Enums\WorkedImagesStatusEnum;
use App\Libraries\CookieInstagram\InstagramService;
use App\Models\InstagramConnects;
use App\Models\PublishingFiles;
use App\Models\WorkedImage;
use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PublishingImagesInstagramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private PublishingFiles $publishingFiles;
    private string $login_instagram;
    private string $password_instagram;
    private ImageService $imageService;
    private InstagramConnects $connect_instagram;
    private  $workedImage;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($workedImage,PublishingFiles $publishingFiles, InstagramConnects $connect_instagram)
    {
        $this->publishingFiles = $publishingFiles;
        $this->connect_instagram = $connect_instagram;
        $this->workedImage = $workedImage;
        $this->imageService = new ImageService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $instagram_service = new \App\Services\InstagramService();
            $photos = $this->workedImage;
            if ($this->publishingFiles->sequence_pictures) {
                $ids = collect($this->publishingFiles->sequence_pictures);
                $photos = $ids->map(function ($id) use ($photos) {
                    return $photos->where('id', $id)->first();
                });
            }
            $post_images_chunk = array_chunk($photos->toArray(), 10);
            foreach ($post_images_chunk as $item_images) {
                if (count($item_images) === 1) {
                    $image = explode('storage/', $item_images[0]['image_url'])[1];
                    $convert_imd_patch = $this->imageService->convertImageForInstagram($image);
                    $url_img = URL::to('/') . '/storage/' . $convert_imd_patch;
                    $instagram_service->publishOnePhoto($this->connect_instagram->instagram_id, $url_img, $this->publishingFiles->description . ' ' . $this->publishingFiles->hashtags);
                    Storage::disk('public')->delete($convert_imd_patch);
                } else {
                 PublishImageInstagramCarusel::dispatch($this->connect_instagram->instagram_id,$item_images, $this->connect_instagram->token, $this->publishingFiles->description . ' ' . $this->publishingFiles->hashtags);
                }
            }
        } catch (\Exception $exception) {
            Log::info('!!!ERROR PublishingImagesInstagramJob' . ' ' . $exception);
        }
    }
}
