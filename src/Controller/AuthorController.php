<?php

namespace App\Controller;

use App\DTO\Author\DTOCreat;
use App\Service\AuthorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidatorException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class AuthorController extends AbstractController
{
    public function __construct(
        protected AuthorService $authorService
    )
    {
    }

    #[Route('/api/author/create', name: 'set_author_create', methods: ['POST'])]
    #[OA\Tag(name: 'Author')]
    #[OA\Post(
        path: '/api/author/create',
        summary: 'Create author',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    ref: new Model(type: DTOCreat::class),
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
                        new OA\Property(property: "firstName", type: "string"),
                        new OA\Property(property: "lastName", type: "string"),
                        new OA\Property(property: "patronymic", type: "string"),
                        new OA\Property(
                            property: "books",
                            type: "array",
                            items: new OA\Items(type: "string")
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
            $dto = $this->authorService->validateDTOCreate($request);
            $data = $this->authorService->createAuthorFromDTO($dto);
            $status = Response::HTTP_CREATED;
        } catch (ValidatorException $e) {
            $data = ['error' => (string)$e->getMessage()];
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

    #[Route('/api/author/list', name: 'get_author_list', methods: ['GET'])]
    #[OA\Tag(name: 'Author')]
    #[OA\Get(
        path: '/api/author/list',
        summary: 'Get author list',
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Page number',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                description: 'Number of items per page',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "items",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "int"),
                                    new OA\Property(property: "firstName", type: "string"),
                                    new OA\Property(property: "lastName", type: "string"),
                                    new OA\Property(property: "patronymic", type: "string"),
                                    new OA\Property(
                                        property: "book_list",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "int"),
                                                new OA\Property(property: "title", type: "string"),
                                                new OA\Property(property: "description", type: "string"),
                                                new OA\Property(property: "image_name", type: "string"),
                                                new OA\Property(property: "publication_date", type: "string"),
                                            ],
                                            type: "object",
                                        )
                                    ),
                                ],
                                type: "object",
                            ),
                        ),
                        new OA\Property(property: "total", type: "integer"),
                        new OA\Property(property: "page", type: "integer"),
                        new OA\Property(property: "limit", type: "integer"),
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: "Bad request",
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
    public function list(Request $request): JsonResponse
    {
        try {
            // Получение параметров пагинации из запроса
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);

            // Вызов метода сервиса для получения списка авторов с пагинацией
            $data = $this->authorService->getAuthorList($page, $limit);
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

    #[Route('/api/author/{id}', name: 'get_author_by_id', methods: ['GET'])]
    #[OA\Tag(name: 'Author')]
    #[OA\Get(
        path: '/api/author/{id}',
        summary: 'Get author by id',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "int"),
                        new OA\Property(property: "firstName", type: "string"),
                        new OA\Property(property: "lastName", type: "string"),
                        new OA\Property(property: "patronymic", type: "string"),
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
    public function show(int $id): JsonResponse
    {
        try {
            $data = $this->authorService->getAuthorByID($id);
            $status = Response::HTTP_OK;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        }

        return $this->json($data, $status);
    }

    #[Route('/api/author/delete/{id}', name: 'delete_author', methods: ['DELETE'])]
    #[OA\Tag(name: 'Author')]
    #[OA\Delete(
        path: '/api/author/delete/{id}',
        summary: 'Delete author',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Author deleted successfully",
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
            $this->authorService->deleteAuthorByID($id);
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
