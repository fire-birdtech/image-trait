<?php

namespace FireBirdTech\HasImage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasImage
{
    public function updateImage(UploadedFile $image): void
    {
        tap($this->image_path, function ($previous) use ($image) {
            $this->forceFill([
                'image_path' => $image->storePublicly(
                    'images', ['disk' => $this->imageDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->imageDisk())->delete($previous);
            }
        });
    }

    public function deleteImage(): void
    {
        Storage::disk($this->imageDisk())->delete($this->image_path);

        $this->forceFill([
            'image_path' => null,
        ])->save();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? Storage::disk($this->imageDisk())->url($this->image_path)
            : $this->defaultImageUrl();
    }

    protected function defaultImageUrl(): string
    {
        return asset('images/default-image.jpg');
    }

    protected function imageDisk(): string
    {
        return 'public';
    }
}
