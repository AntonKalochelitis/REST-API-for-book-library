<?php

namespace App\Traits\DTO\Book;

use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator as Validator;

trait ImageName
{
    #[Property(type: 'file')]
    #[Validator\Constraints\Image(maxSize: '2M', mimeTypes: ['image/jpg', 'image/jpeg', 'image/png'])]
    protected ?UploadedFile $imageName = null;

    /**
     * Возвращает имя изображения
     *
     * @return UploadedFile|null
     */
    public function getImageName(): ?UploadedFile
    {
        return $this->imageName;
    }

    /**
     * Устанавливает имя изображения
     *
     * @param UploadedFile $imageName
     * @return \App\DTO\Book\DTOCreate|ImageName
     */
    public function setImageName(UploadedFile $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }
}