<?php

namespace App\Traits\DTO\Author;

use Symfony\Component\Validator as Validator;

/**
 * @property string $first_name
 */
trait FirstName
{
    #[Validator\Constraints\NotBlank(message: "First name is required")]
    #[Validator\Constraints\Length(
        min: "2",
        max: "256",
        minMessage: "Your first_name must be at least {{ limit }} characters long",
        maxMessage: "Your first_name cannot be longer than {{ limit }} characters"
    )]
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    protected string $first_name = '';

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }
}