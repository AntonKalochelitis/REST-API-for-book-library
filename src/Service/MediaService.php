<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaService
{
    public function __construct(
        protected BookRepository $bookRepository,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param string $name
     * @param string $filesDir
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getImageByName(string $name, string $filesDir):string
    {
        $book = $this->bookRepository->findByImageName($name);
        if (!$book) {
            throw new NotFoundHttpException('Book with ImageName ' . $name . ' not found.');
        }

        // Полный путь к файлу
        $filePath = $filesDir . DIRECTORY_SEPARATOR . $name;

        // Создаем экземпляр Filesystem
        $filesystem = new Filesystem();

        // Проверяем, существует ли файл и удаляем его
        if ($filesystem->exists($filePath)) {
            $data = base64_encode(file_get_contents($filePath));
        } else {
            throw new NotFoundHttpException('File with ImageName ' . $name . ' not found.');
        }

        return $data;
    }

    /**
     * @param string $name
     * @param string $filesDir
     * @return Book
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteImageByName(string $name, string $filesDir):Book
    {
        $book = $this->bookRepository->findByImageName($name);
        if (!$book) {
            throw new NotFoundHttpException('Book with ImageName ' . $name . ' not found.');
        }
        $book->setImageName('');

        // Сохраняем изменения в базе данных
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        // Полный путь к файлу
        $filePath = $filesDir . DIRECTORY_SEPARATOR . $name;

        // Создаем экземпляр Filesystem
        $filesystem = new Filesystem();

        // Проверяем, существует ли файл и удаляем его
        if ($filesystem->exists($filePath)) {
            try {
                $filesystem->remove($filePath);
            } catch (\Exception $e) {
                throw new HttpException('Error delete file');
            }
        } else {
            throw new NotFoundHttpException('File with ImageName ' . $name . ' not found.');
        }

        return $book;
    }
}