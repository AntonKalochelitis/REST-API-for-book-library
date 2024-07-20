<?php

namespace App\Service;

use App\DTO\Author\DTOCreat;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorService
{
    public function __construct(
        protected AuthorRepository $authorRepository,
        protected ValidatorInterface  $validator,
        protected SerializerInterface $serializer,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Валидация и создание DTO для создания автора из запроса.
     *
     * @param Request $request
     * @return DTOCreat
     */
    public function validateDTOCreate(Request $request): DTOCreat
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            DTOCreat::class,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidatorException($errors);
        }

        return $dto;
    }

    /**
     * Создание автора
     *
     * @param DTOCreat $dto
     * @return Author
     */
    public function createAuthorFromDTO(DTOCreat $dto): Author
    {
        $author = new Author();
        $author->setFirstName($dto->getFirstName());
        $author->setLastName($dto->getLastName());

        if (!empty($dto->getPatronymic())) {
            $author->setPatronymic($dto->getPatronymic());
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

    /**
     * Получение списка всех авторов
     *
     * @return array
     */
    public function getAuthorList(): array
    {
        $result = [];

        foreach ($this->authorRepository->findAll() as $author) {
            $result[] = [
                'id' => $author->getId(),
                'first_name' => $author->getFirstName(),
                'last_name' => $author->getLastName(),
                'patronymic' => $author->getPatronymic(),
                'book_list' => array_map(function (Book $book) {
                    return [
                        'id' => $book->getId(),
                        'title' => $book->getTitle(),
                        'description' => $book->getDescription(),
                        'image_name' => $book->getImageName(),
                        'publication_date' => $book->getPublicationDate(),
                    ];
                }, $author->getBooks()->toArray()),
            ];
        }

        return $result;
    }

    /**
     * Получение автора по ID
     *
     * @param int $id
     * @return array
     */
    public function getAuthorByID(int $id): array
    {
        $author = $this->authorRepository->find($id);
        if (empty($author)) {
            throw new NotFoundHttpException('Author not found');
        }

        return [
            'id' => $author->getId(),
            'first_name' => $author->getFirstName(),
            'last_name' => $author->getLastName(),
            'patronymic' => $author->getPatronymic(),
        ];
    }
}