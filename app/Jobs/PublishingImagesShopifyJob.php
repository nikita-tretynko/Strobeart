<?php

namespace App\Jobs;

use App\Enums\WorkedImagesStatusEnum;
use App\Models\PublishingFiles;
use App\Models\User;
use App\Models\WorkedImage;
use App\Services\ShopifyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishingImagesShopifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var PublishingFiles
     */
    private PublishingFiles $publishingFiles;
    /**
     * @var array
     */
    private array $shopify_login;
    /**
     * @var
     */
    private $product_id;
    /**
     * @var User
     */
    private User $user;

    private string $alt_text;

    private string $description;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, PublishingFiles $publishingFiles, array $shopify_login, $product_id,$description=null, $alt_text = null)
    {
        $this->shopify_login = $shopify_login;
        $this->publishingFiles = $publishingFiles;
        $this->product_id = $product_id;
        $this->user = $user;
        $this->description = $description;
        $this->alt_text = $alt_text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->user->load('shopify_connect');
            $token = $this->user->shopify_connect->token;
            $shop = $this->user->shopify_connect->shop;
            $shopify_service = new ShopifyService();

            $photos = WorkedImage::where('image_jobs_id', $this->publishingFiles->image_jobs_id)
                ->where('status', WorkedImagesStatusEnum::$FINISHED)->get();
            if ($this->publishingFiles->sequence_pictures) {
                $ids = collect($this->publishingFiles->sequence_pictures);
                $photos = $ids->map(function ($id) use ($photos) {
                    return $photos->where('id', $id)->first();
                });
            }
            foreach ($photos as $photo) {
                $image = explode('storage/', $photo->image_url)[1];
                $image_patch = storage_path('app/public/' . $image);
                $shopify_service->addImagesToProduct($token, $shop, $this->product_id, $this->codeImageIn64($image_patch,null, $this->alt_text));
            }

        } catch (\Exception $exception) {
            Log::info('!!!ERROR PublishingImagesInstagramJob' . ' ' . $exception);
        }
    }

    /**
     * @param string $real_image_path
     * @param string|null $image_name
     * @param string|null $alt_text
     * @return array
     */
    private function codeImageIn64(string $real_image_path, string $image_name = null, string $alt_text = null): array
    {
        $file = file_get_contents($real_image_path);
        $base64_code = base64_encode($file);
        $image_name = $image_name ?? collect(explode('/', $real_image_path))->last();

        return [
            'attachment' => $base64_code,
            'filename' => $image_name,
            'metafields' => [
                [
                    "key" => "alt",
                    "value" => $alt_text,
                    "type" => "string",
                    "namespace" => "tags"
                ]
            ]
        ];
    }
}
