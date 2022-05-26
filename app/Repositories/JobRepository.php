<?php

namespace App\Repositories;

use App\Models\ImageJob;

/**
 * Class JobRepository
 * @package App\Repositories
 */
class JobRepository extends CoreRepository
{
    /**
     * Get ImageJob with Images
     *
     * @param int
     * @param bool $filtered
     *
     * @return ImageJob
     */
    public function getJobWithImages(int $id, bool $filtered = false)
    {
        if (!$filtered) {
            $job = ImageJob::where('id', $id)
                ->with('images.worked_img')
                ->first();
        } else {
            $job = ImageJob::where('id', $id)
                ->with([
                    'images' => function ($query) {
                        $query->whereNotIn('decline', [1]);
                    }, 'images.worked_img'])
                ->first();
        }

        return $job;
    }

    /**
     * @inheritDoc
     */
    protected function getModelClass()
    {
        return ImageJob::class;
    }
}
