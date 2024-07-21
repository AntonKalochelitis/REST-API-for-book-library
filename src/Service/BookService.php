<?php

namespace App\Service;

use App\DTO\Book\DTOCreate;
use App\DTO\Book\DTOSearch;
use App\DTO\Book\DTOUpdate;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService
{
    public function __construct(
        protected BookRepository         $bookRepository,
        protected AuthorRepository       $authorRepository,
        protected ValidatorInterface     $validator,
        protected SerializerInterface    $serializer,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Валидация и создание DTO для создания книги из запроса.
     *
     * @param Request $request
     * @return DTOCreate
     */
    public function validateDTOCreate(Request $request): DTOCreate
    {
        $dto = new DTOCreate();
        $dto->setTitle($request->request->get('title'));
        $dto->setDescription($request->request->get('description'));
        $dto->setPublicationDate($request->request->get('publication_date'));
        $dto->setAuthorIds(json_decode($request->request->get('authorIDList'), true));

        if ($request->files->get('image')) {
            $dto->setImageName($request->files->get('image')->getClientOriginalName());
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidatorException($errors);
        }

        return $dto;
    }

    /**
     * Создание книги
     *
     * @param Request $request
     * @param string $images_directory
     * @param DTOCreate $dto
     * @return array
     * @throws \Exception
     */
    public function createBookFromDTO(Request $request, string $images_directory, DTOCreate $dto): array
    {
        $book = new Book();
        $book->setTitle($dto->getTitle());
        $book->setDescription($dto->getDescription());

        if ($request->files->get('imageName')) {
            $image = $request->files->get('imageName');

            $imageName = md5(uniqid()) . '.' . $image->guessExtension();

            $image->move($images_directory, $imageName);
            $book->setImageName($imageName);
        }

        if (!empty($dto->getPublicationDate())) {
            $book->setPublicationDate($dto->getPublicationDate());
        }

        foreach ($dto->getAuthorIds() as $authorId) {
            $author = $this->authorRepository->find($authorId);
            if ($author) {
                $book->addAuthor($author);
            }
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'image_name' => $book->getImageName(),
            'publication_date' => $book->getPublicationDate(),
            'authors' => array_map(function (Author $author) {
                return [
                    'id' => $author->getId(),
                    'first_name' => $author->getFirstName(),
                    'last_name' => $author->getLastName(),
                    'patronymic' => $author->getPatronymic(),
                ];
            }, $book->getAuthors()->toArray()),
        ];
    }

    /**
     * Получение списка всех книг
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getBookList(int $page, int $limit): array
    {
        $queryBuilder = $this->bookRepository->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->addSelect('a')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, true);
        $totalItems = count($paginator);
        $bookList = iterator_to_array($paginator);

        $result = array_map(function (Book $book) {
            return [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'image_name' => $book->getImageName(),
                'publication_date' => $book->getPublicationDate(),
                'authors' => array_map(function (Author $author) {
                    return [
                        'id' => $author->getId(),
                        'first_name' => $author->getFirstName(),
                        'last_name' => $author->getLastName(),
                        'patronymic' => $author->getPatronymic(),
                    ];
                }, $book->getAuthors()->toArray()),
            ];
        }, $bookList);

        return [
            'items' => $result,
            'total' => $totalItems,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function validateDTOSearch(Request $request): DTOSearch
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            DTOSearch::class,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidatorException($errors);
        }

        return $dto;
    }

    public function searchBooksByAuthor(DTOSearch $dto)
    {
        $queryBuilder = $this->bookRepository->createQueryBuilder('b');
        $queryBuilder
            ->leftJoin('b.authors', 'a')
            ->addSelect('a');

        if ($dto->getSearch()) {
            $search = '%' . $dto->getSearch() . '%';

            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('b.title', ':search'),
                        $queryBuilder->expr()->like('a.firstName', ':search'),
                        $queryBuilder->expr()->like('a.lastName', ':search'),
                        $queryBuilder->expr()->like('a.patronymic', ':search')
                    )
                )
                ->setParameter('search', $search);
        }

        $books = $queryBuilder->getQuery()->getResult();

        return array_map(function ($book) {
            return [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'image_name' => $book->getImageName(),
                'publication_date' => $book->getPublicationDate(),
                'authors' => array_map(function ($author) {
                    return [
                        'id' => $author->getId(),
                        'first_name' => $author->getFirstName(),
                        'last_name' => $author->getLastName(),
                        'patronymic' => $author->getPatronymic(),
                    ];
                }, $book->getAuthors()->toArray()),
            ];
        }, $books);
    }

    /**
     * Получение книги по ID
     *
     * @param int $id
     * @return array
     */
    public function getBookByID(int $id): array
    {
        $book = $this->bookRepository->find($id);
        if (empty($book)) {
            throw new NotFoundHttpException('Book not found');
        }

        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'image_name' => $book->getImageName(),
            'publication_date' => $book->getPublicationDate(),
            'authors' => array_map(function ($author) {
                return [
                    'id' => $author->getId(),
                    'first_name' => $author->getFirstName(),
                    'last_name' => $author->getLastName(),
                    'patronymic' => $author->getPatronymic(),
                ];
            }, $book->getAuthors()->toArray()),
        ];
    }

    public function validateDTOUpdate(Request $request): DTOUpdate
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            DTOUpdate::class,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidatorException($errors);
        }

        return $dto;
    }

    public function updateBooksByID(int $id, DTOUpdate $dto): array
    {
        $book = $this->bookRepository->find($id);
        if (!$book) {
            throw new NotFoundHttpException('Book not found');
        }

        if ($dto->getTitle()) {
            $book->setTitle($dto->getTitle());
        }

        if ($dto->getDescription()) {
            $book->setDescription($dto->getDescription());
        }

        if (!empty($dto->getPublicationDate())) {
            $book->setPublicationDate($dto->getPublicationDate());
        }

        if (count($dto->getAuthorIds()) > 0) {
            $book->getAuthors()->clear();
            foreach ($dto->getAuthorIds() as $authorId) {
                $author = $this->authorRepository->find($authorId);
                if ($author) {
                    $book->addAuthor($author);
                }
            }
        }

        $this->entityManager->flush();

        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'image_name' => $book->getImageName(),
            'publication_date' => $book->getPublicationDate(),
            'authors' => array_map(function ($author) {
                return [
                    'id' => $author->getId(),
                    'first_name' => $author->getFirstName(),
                    'last_name' => $author->getLastName(),
                    'patronymic' => $author->getPatronymic(),
                ];
            }, $book->getAuthors()->toArray()),
        ];
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteBookByID(int $id): void
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            throw new NotFoundHttpException("Book with ID $id not found.");
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}