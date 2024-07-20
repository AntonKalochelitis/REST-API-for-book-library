<?php

namespace App\DTO\Book;

use App\Traits\DTO\Book\AuthorIDList;
use App\Traits\DTO\Book\Description;
use App\Traits\DTO\Book\PublicationDate;
use Symfony\Component\Validator as Validator;

class DTOUpdate
{
    #[Validator\Constraints\Length(min: 2, minMessage: "Title must be at least 2 characters long")]
    #[Validator\Constraints\Type(
        type: "string",
        message: "The value {{ value }} is not a valid {{ type }}."
    )]
    protected ?string $title = null;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    use Description;
    use PublicationDate;
    use AuthorIDList;
}