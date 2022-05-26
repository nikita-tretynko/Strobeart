<?php

namespace App\Repositories;

use App\Models\Image;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class ImageRepository
 * @package App\Repositories
 */
class ImageRepository extends CoreRepository
{
    /**
     * Approve or decline images
     *
     * @param array $images
     * @param string|null $message
     *
     * @return void
     *
     * @throws Exception
     */
    public function approveOrDeclineImages(array $images, string $message = null)
    {
        try {
            DB::beginTransaction();

            foreach ($images as $image) {
                $old_image = Image::find($image['id']);
                $old_image->approval = $image['approval'];
                $old_image->decline = $image['decline'];

                if ($image['decline'] && $message) {
                    $old_image->message = $message;
                }

                $old_image->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @inheritDoc
     */
    protected function getModelClass()
    {
        return Image::class;
    }
}
