<?php

namespace App\Traits\DTO\Author;

use Symfony\Component\Validator as Validator;

/**
 * @property string $last_name
 */
trait LastName
{
    #[Validator\Constraints\NotBlank(message: "Last name is required")]
    #[Validator\Constraints\Length(
        min: "2",
        max: "256",
        minMessage: "Your last_name must be at least {{ limit }} characters long",
        maxMessage: "Your last_name cannot be longer than {{ limit }} characters"
    )]
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    protected string $last_name = '';

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }
}