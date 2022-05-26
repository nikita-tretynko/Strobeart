<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ChatImage extends Model
{
    use HasFactory;

    private static string $path = 'chat';

    protected $fillable = ['user_id','job_image_id','file_name'];
    /**
     * @var string[]
     */
    protected $appends = ['url'];

    /**
     * @return string|null
     */
    public function getUrlAttribute(): ?string
    {
        return $this->file_name?URL::to('/') . '/storage/' . $this->file_name: null;
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->file_name) {
            Storage::disk('public')->delete($this->file_name);
        }
        return parent::delete();
    }

    public function updateChatImage(UploadedFile $image): void
    {
        $this->file_name = Storage::disk('public')->put(self::$path, $image);
    }
}

