<?php

namespace App\Traits\DTO\Book;

use Symfony\Component\Validator\Constraints as Validator;

trait AuthorIDList
{
    #[Validator\All([
        new Validator\Type('integer')
    ])]
    protected array $authorIDList = [];

    /**
     * @return array
     */
    public function getAuthorIds(): array
    {
        return $this->authorIDList;
    }

    /**
     * @param array $authorIDList
     * @return \App\DTO\Book\DTOCreate|AuthorIDList
     */
    public function setAuthorIds(array $authorIDList): self
    {
        $this->authorIDList = $authorIDList;

        return $this;
    }
}