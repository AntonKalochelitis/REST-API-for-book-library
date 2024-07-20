<?php

namespace App\Traits\DTO\Book;

use Symfony\Component\Validator as Validator;

trait Title
{
    #[Validator\Constraints\NotBlank(message: "Title is required")]
    #[Validator\Constraints\Length(min: 2, minMessage: "Title must be at least 2 characters long")]
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    protected string $title = '';

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \App\DTO\Book\DTOCreate|Title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}