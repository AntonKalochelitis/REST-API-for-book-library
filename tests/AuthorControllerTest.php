<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorControllerTest extends WebTestCase
{
    public function testListAuthorsSuccess()
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_GET,
            '/api/author/list'
        );

        $this->assertResponseIsSuccessful();

        $client->request(
            Request::METHOD_GET,
            '/api/author/list?page=1&limit=10'
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertArrayHasKey('page', $response);
        $this->assertArrayHasKey('limit', $response);
    }

    public function testCreateAuthorSuccess()
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_POST,
            '/api/author/create',
            ['headers' => [
                'Content-Type' => 'application/json'
            ]],
            [],
            [],
            json_encode([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'patronymic' => 'Middle',
                'books' => ['Book1', 'Book2']
            ])
        );

        $this->assertEquals(
            Response::HTTP_CREATED,
            $client->getResponse()->getStatusCode()
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('John', $response['firstName']);

        // Выполняем тест проверяющий наличие автора
        $this->showAuthorSuccess($client, $response['id']);

        // Выполняем тест удаления
        $this->deleteAuthorSuccess($client, $response['id']);
    }

    public function testCreateAuthorValidationError()
    {
        $client = static::createClient();
        $client->request(
            Request::METHOD_POST,
            '/api/author/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $client->getResponse()->getStatusCode()
        );
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
    }

    public function showAuthorSuccess($client, int $id)
    {
        $client->request('GET', '/api/author/' . $id);

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
        $response = json_decode(
            $client->getResponse()->getContent(),
            true
        );
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('first_name', $response);
        $this->assertArrayHasKey('last_name', $response);
        $this->assertArrayHasKey('patronymic', $response);
    }

    public function deleteAuthorSuccess($client, int $id)
    {
        $client->request('DELETE', '/api/author/delete/' . $id);

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('success', $response['status']);
    }
}