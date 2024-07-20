<?php

namespace App\DTO\Book;

use Symfony\Component\Validator as Validator;

/**
 * @property string $search
 */
class DTOSearch
{
    #[Validator\Constraints\NotBlank(message: "Search is required")]
    #[Validator\Constraints\Length(
        min: "2",
        max: "256",
        minMessage: "Your search must be at least {{ limit }} characters long",
        maxMessage: "Your search cannot be longer than {{ limit }} characters"
    )]
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    private ?string $search = null;

    /**
     * @param string|null $search
     * @return $this
     */
    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }
}