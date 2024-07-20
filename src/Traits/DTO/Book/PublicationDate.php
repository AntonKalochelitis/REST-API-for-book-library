<?php

namespace App\Traits\DTO\Book;

use Symfony\Component\Validator as Validator;

trait PublicationDate
{
    #[Validator\NotBlank]
    #[Validator\Date]
    protected ?\DateTimeImmutable $publicationDate = null;

    /**
     * @return \DateTimeImmutable|null
     * @throws \Exception
     */
    public function getPublicationDate(): ?\DateTimeImmutable
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTimeImmutable|null $publicationDate
     * @return \App\DTO\Book\DTOCreate|PublicationDate
     */
    public function setPublicationDate(?\DateTimeImmutable $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }
}