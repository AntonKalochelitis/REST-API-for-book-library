<?php

namespace App\Controller;

use App\Service\MediaService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class MediaController extends AbstractController
{
    public function __construct(protected MediaService $mediaService)
    {
    }

    #[Route('/api/book/media/image/{name}', name: 'get_image_by_name', methods: ['GET'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Get(
        path: '/api/book/media/image/{name}',
        summary: 'Get image',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Image success",
                content: new OA\JsonContent(
                    type: "string",
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
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
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
    public function create(string $name): JsonResponse
    {
        try {
            $data = $this->mediaService->getImageByName($name, $this->getParameter('images_directory'));
            $status = Response::HTTP_OK;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        } catch (NonUniqueResultException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_CONFLICT;
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        }
        return $this->json($data, $status);
    }

    #[Route('/api/book/media/image/{name}', name: 'delete_image_by_name', methods: ['DELETE'])]
    #[OA\Tag(name: 'Book')]
    #[OA\Delete(
        path: '/api/book/media/image/{name}',
        summary: 'Delete image',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Image deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "status", type: "string")
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
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ],
                    type: "object",
                )
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
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
    public function delete(string $name): JsonResponse
    {
        try {
            $this->mediaService->deleteImageByName($name, $this->getParameter('images_directory'));

            $data = [
                'message' => 'Delete Name:' . $name . ' is OK',
                'status' => 'success'
            ];
            $status = Response::HTTP_OK;
        } catch (NotFoundHttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_NOT_FOUND;
        } catch (HttpException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        } catch (NonUniqueResultException $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_CONFLICT;
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $status = Response::HTTP_BAD_REQUEST;
        }
        return $this->json($data, $status);
    }
}