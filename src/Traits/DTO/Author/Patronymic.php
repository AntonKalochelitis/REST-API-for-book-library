<?php

namespace App\Traits\DTO\Author;

use Symfony\Component\Validator as Validator;

/**
 * @property string $patronymic
 */
trait Patronymic
{
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    protected ?string $patronymic = null;

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }
}