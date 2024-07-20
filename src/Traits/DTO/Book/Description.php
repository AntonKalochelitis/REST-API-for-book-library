<?php

namespace App\Traits\DTO\Book;

use Symfony\Component\Validator as Validator;

trait Description
{
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    protected ?string $description = null;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Description|\App\DTO\Book\DTOCreate
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}