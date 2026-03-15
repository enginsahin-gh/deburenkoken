<?php

namespace App\Repositories;

use App\Dtos\ImageDto;
use App\Models\Dish;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Collection;

class ImageRepository
{
    private Image $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function find(string $uuid): ?Image
    {
        $image = $this->image->find($uuid);

        if (! $image) {
            $image = $this->image->withTrashed()->find($uuid);
        }

        return $image;
    }

    public function create(ImageDto $imageDto): Image
    {
        // Voor hoofdafbeeldingen: verwijder oude hoofdafbeelding EERST
        if ($imageDto->isMainPicture()) {
            $query = $this->image
                ->where('user_uuid', $imageDto->getUserUuid())
                ->where('type_id', $imageDto->getTypeId())
                ->where('main_picture', true)
                ->whereNull('deleted_at');

            // BUG FIX BL-211: Voor gerecht-afbeeldingen, filter ook op dish_uuid
            // Anders worden afbeeldingen van ANDERE gerechten van dezelfde gebruiker verwijderd
            if ($imageDto->getTypeId() === Image::DISH_IMAGE && $imageDto->getDish() !== null) {
                $query->where('dish_uuid', $imageDto->getDish()->getUuid());
            }

            $query->delete(); // Soft delete
        }

        $image = $this->image->create([
            'user_uuid' => $imageDto->getUserUuid(),
            'dish_uuid' => $imageDto->getDish()?->getUuid(),
            'type_id' => $imageDto->getTypeId(),
            'main_picture' => $imageDto->isMainPicture(),
            'path' => $imageDto->getPath(),
            'name' => $imageDto->getName(),
            'description' => $imageDto->getDescription(),
            'type' => $imageDto->getType(),
        ]);

        return $image;
    }

    public function delete(string $uuid): ?bool
    {
        try {
            $image = $this->find($uuid);

            if (! $image) {
                return true;
            }

            if ($image->deleted_at !== null) {
                return $image->forceDelete();
            }

            return $image->delete();

        } catch (\Exception $e) {
            \Log::warning('Image delete failed for UUID: '.$uuid.' - '.$e->getMessage());

            return true;
        }
    }

    public function getUserProfileImages(string $userUuid): Collection
    {
        return $this->image
            ->where('user_uuid', $userUuid)
            ->where('type_id', Image::PROFILE_IMAGE)
            ->where('main_picture', false) // ALLEEN aanvullende afbeeldingen
            ->whereNull('deleted_at')
            ->get();
    }

    public function getAdditionalProfileImagesCount(string $userUuid): int
    {
        return $this->image
            ->where('user_uuid', $userUuid)
            ->where('type_id', Image::PROFILE_IMAGE)
            ->where('main_picture', false)
            ->whereNull('deleted_at')
            ->count();
    }

    public function findMainProfileImage(string $userUuid): ?Image
    {
        return $this->image
            ->where('user_uuid', $userUuid)
            ->where('type_id', Image::PROFILE_IMAGE)
            ->where('main_picture', true)
            ->whereNull('deleted_at')
            ->first();
    }

    public function findDishMainImage(string $dishUuid): ?Image
    {
        return $this->image
            ->where('dish_uuid', $dishUuid)
            ->where('type_id', Image::DISH_IMAGE)
            ->where('main_picture', true)
            ->whereNull('deleted_at')
            ->first();
    }

    public function hasProfileImagesWithMain(?string $mainImage): bool
    {
        // Deze methode is niet meer nodig met nieuwe logica
        return false;
    }

    public function getProfileImagePath(string $userUuid): string
    {
        $mainImage = $this->findMainProfileImage($userUuid);

        if ($mainImage && file_exists(public_path($mainImage->getCompletePath()))) {
            return $mainImage->getCompletePath();
        }

        return url('/img/kok.png');
    }

    public function getDishImagePath(string $dishUuid): string
    {
        $mainImage = $this->findDishMainImage($dishUuid);

        if ($mainImage) {
            return $mainImage->getCompletePath();
        }

        return asset('img/defaults/pasta.jpg');
    }

    public function getProfileImageUrl(string $userUuid): string
    {
        $mainImage = $this->findMainProfileImage($userUuid);

        if ($mainImage) {
            return url($mainImage->getCompletePath());
        }

        return asset('img/defaults/kok.png');
    }

    public function getDishImageUrl(string $dishUuid): string
    {
        $mainImage = $this->findDishMainImage($dishUuid);

        if ($mainImage) {
            return url($mainImage->getCompletePath());
        }

        return asset('img/defaults/pasta.jpg');
    }
}
