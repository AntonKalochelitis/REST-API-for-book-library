<?php

namespace App\Controller;

use App\DTO\Book\DTOCreate;
use App\DTO\Book\DTOSearch;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidatorException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class BookController extends AbstractController
{
    public function __construct(
        protected BookService $bookService
    )
    {

    }

    #[Route('/api/book/create', name: 'create_book', methods: ['POST'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Post(
        path: '/api/book/create',
        summary: 'Create book',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: new Model(type: DTOCreate::class),
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Author created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "int"),
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "image_name", type: "string"),
                        new OA\Property(property: "publication_date", type: "string"),
                        new OA\Property(
                            property: "authors",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "int"),
                                    new OA\Property(property: "first_name", type: "string"),
                                    new OA\Property(property: "last_name", type: "string"),
                                    new OA\Property(property: "patronymic", type: "string")
                                ],
                                type: "object",
                            )
                        ),
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            )
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            $dto = $this->bookService->validateDTOCreate($request);
            $data = $this->bookService->createBookFromDTO(
                $request,
                $this->getParameter('images_directory'),
                $dto
            );
            $status = Response::HTTP_CREATED;
        } catch (ValidatorException $e) {
            $data = ['error' => (string)$e->getMessage()];
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        } catch (FileException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        } catch (\Exception $e) {
            $data = [
                'error' => $e->getTrace()
            ];
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($data, $status);
    }

    #[Route('/api/books/search', name: 'search_books', methods: ['POST'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Post(
        path: '/api/books/search',
        summary: 'search books',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    ref: new Model(type: DTOSearch::class),
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "int"),
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "image_name", type: "string"),
                        new OA\Property(property: "publication_date", type: "string"),
                        new OA\Property(
                            property: "authors",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "int"),
                                    new OA\Property(property: "first_name", type: "string"),
                                    new OA\Property(property: "last_name", type: "string"),
                                    new OA\Property(property: "patronymic", type: "string"),
                                ],
                                type: "object",
                            )
                        ),
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            )
        ]
    )]
    public function search(Request $request): JsonResponse
    {
        try {
            $dto = $this->bookService->validateDTOSearch($request);
            $data = $this->bookService->searchBooksByAuthor($dto);
            $status = Response::HTTP_OK;
        } catch (ValidatorException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($data, $status);
    }

    #[Route('/api/book/list', name: 'get_book_list', methods: ['GET'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Get(
        path: '/api/book/list',
        summary: 'Get book list',
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Page number',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Number of items per page',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successfully",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "items", type: "array", items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "int"),
                                    new OA\Property(property: "title", type: "string"),
                                    new OA\Property(property: "description", type: "string"),
                                    new OA\Property(property: "image_name", type: "string"),
                                    new OA\Property(property: "publication_date", type: "string"),
                                    new OA\Property(
                                        property: "authors",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "int"),
                                                new OA\Property(property: "first_name", type: "string"),
                                                new OA\Property(property: "last_name", type: "string"),
                                                new OA\Property(property: "patronymic", type: "string"),
                                            ],
                                            type: "object",
                                        )
                                    ),
                                ],
                                type: "object",
                            )),
                            new OA\Property(property: "total", type: "integer"),
                            new OA\Property(property: "page", type: "integer"),
                            new OA\Property(property: "limit", type: "integer"),
                        ],
                        type: "object",
                    ),
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            )
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        try {
            $data = $this->bookService->getBookList($page, $limit);
            $status = Response::HTTP_OK;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($data, $status);
    }

    #[Route('/api/book/{id}', name: 'get_book_by_id', methods: ['GET'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Get(
        path: '/api/book/{id}',
        summary: 'Get book by id',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successfully",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "int"),
                            new OA\Property(property: "title", type: "string"),
                            new OA\Property(property: "description", type: "string"),
                            new OA\Property(property: "image_name", type: "string"),
                            new OA\Property(property: "publication_date", type: "string"),
                            new OA\Property(
                                property: "authors",
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "id", type: "int"),
                                        new OA\Property(property: "firstName", type: "string"),
                                        new OA\Property(property: "lastName", type: "string"),
                                        new OA\Property(property: "patronymic", type: "string"),
                                    ],
                                    type: "object",
                                )
                            ),
                        ],
                        type: "object",
                    )
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: "NOT FOUND",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $data = $this->bookService->getBookByID($id);
            $status = Response::HTTP_OK;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        }

        return $this->json($data, $status);
    }

    #[Route('/api/book/{id}', name: 'update_book_by_id', methods: ['PUT'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Put(
        path: '/api/book/{id}',
        summary: 'Update book by id',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successfully",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "int"),
                            new OA\Property(property: "title", type: "string"),
                            new OA\Property(property: "description", type: "string"),
                            new OA\Property(property: "image_name", type: "string"),
                            new OA\Property(property: "publication_date", type: "string"),
                            new OA\Property(
                                property: "authors",
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(type: "int"),
                                    ],
                                    type: "object",
                                )
                            )
                        ],
                        type: "object",
                    )
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            )
        ],
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $dto = $this->bookService->validateDTOUpdate($request);
            $data = $this->bookService->updateBooksByID($id, $dto);
            $status = Response::HTTP_OK;
        } catch (ValidatorException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($data, $status);
    }

    #[Route('/api/book/delete/{id}', name: 'delete_book', methods: ['DELETE'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Delete(
        path: '/api/book/delete/{id}',
        summary: 'Delete book',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Book deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "status", type: "string")
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->bookService->deleteBookByID($id);
            $data = [
                'message' => 'Delete ID:' . $id . ' is OK',
                'status' => 'success'
            ];
            $status = Response::HTTP_OK;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->json($data, $status);
    }
}