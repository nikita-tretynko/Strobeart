<?php

namespace App\Jobs;

use App\Services\ImageService;
use App\Services\InstagramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PublishImageInstagramCarusel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ImageService $imageService;
    private InstagramService $instagram_service;
    private $instagram_id;
    private $item_images;
    private $connect_instagram_token;
    private $capture;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($instagram_id, $item_images, $connect_instagram_token, $capture)
    {
        $this->imageService = new ImageService();
        $this->instagram_service = new InstagramService();
        $this->instagram_id = $instagram_id;
        $this->item_images = $item_images;
        $this->connect_instagram_token = $connect_instagram_token;
        $this->capture = $capture;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id_img = [];
        foreach ($this->item_images as $image) {
            $image1 = explode('storage/', $image['image_url'])[1];
            $convert_imd_patch = $this->imageService->convertImageForInstagram($image1);
            $url_img = URL::to('/') . '/storage' . $convert_imd_patch;
            Log::info('publish image2: '. $image['image_url']);
            $id_cont = $this->instagram_service->createItemContainer($this->instagram_id, $url_img, $this->connect_instagram_token);
            $id_img[] = $id_cont;
            Storage::disk('public')->delete($convert_imd_patch);
        }
        Log::info('!!!container '.json_encode($id_img));
        $id_container = $this->instagram_service->createCarouselContainer($this->instagram_id, $id_img, $this->connect_instagram_token, $this->capture);
        $this->instagram_service->publishCarouselContainer($this->instagram_id, $id_container, $this->connect_instagram_token);
    }
}
