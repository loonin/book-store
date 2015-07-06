<?php

namespace Intaro\BookStoreBundle\Controller;

use Intaro\BookStoreBundle\Entity\Book;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function listAction(Request $request)
    {
        $serializer = SerializerBuilder::create()->build();
        $queryData = $request->query->all();

        $data = array(
            'success' => false,
            'error' => 'Ошибка добавления книги'
        );

        if (empty($queryData['apiKey'])
            || !$this->checkApiKey($queryData['apiKey'])
        ) {
            $data['error'] = 'Неверный ключ';

            $jsonContent = $serializer->serialize($data, 'json');

            return new Response($jsonContent);
        }

        $books = $this->getDoctrine()
            ->getRepository('IntaroBookStoreBundle:Book')
            ->findAllOrderedByName();

        foreach ($books as $book) {
            if ($pathToFile = $book->getFile()) {
                if (!$book->getDownload()) {
                    $book->setFile(null);
                } else {
                    $book->setFile('http://' . $_SERVER['SERVER_NAME'] . $book->getWebPath('file'));
                }
            }

            if ($pathToCover = $book->getCover()) {
                $book->setCover('http://' . $_SERVER['SERVER_NAME'] . $book->getWebPath('cover'));
            }
        }

        $data = array(
            'success' => true,
            'data' => $books
        );

        $jsonContent = $serializer->serialize($data, 'json');

        return new Response($jsonContent);
    }

    public function addAction(Request $request)
    {
        $serializer = SerializerBuilder::create()->build();

        $data = array(
            'success' => false,
            'error' => 'Ошибка добавления книги'
        );

        if ($request->getMethod() == 'POST') {
            $requestData = $request->request->all();

            if (empty($requestData['apiKey'])
                || !$this->checkApiKey($requestData['apiKey'])
            ) {
                $data['error'] = 'Неверный ключ';

                $jsonContent = $serializer->serialize($data, 'json');

                return new Response($jsonContent);
            }

            $book = new Book();

            $requestData['download'] = (int)$requestData['download'];

            if (!empty($requestData['name'])
                && !empty($requestData['author'])
                && !empty($requestData['readingDate'])
                && isset($requestData['download'])
            ) {
                $book->setName($requestData['name']);
                $book->setAuthor($requestData['author']);
                $book->setReadingDate(new \DateTime($requestData['readingDate']));
                $book->setDownload($requestData['download']);
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($book);
            $em->flush();

            $data = array(
                'success' => true,
                'message' => 'Книга успешно добавлена'
            );
        }

        $jsonContent = $serializer->serialize($data, 'json');

        return new Response($jsonContent);
    }

    public function editAction(Request $request, $id)
    {
        $serializer = SerializerBuilder::create()->build();

        $data = array(
            'success' => false,
            'error' => 'Ошибка обновления книги'
        );

        if ($request->getMethod() == 'POST') {
            $requestData = $request->request->all();

            if (empty($requestData['apiKey'])
                || !$this->checkApiKey($requestData['apiKey'])
            ) {
                $data['error'] = 'Неверный ключ';

                $jsonContent = $serializer->serialize($data, 'json');

                return new Response($jsonContent);
            }


            $book = $this->getDoctrine()
                ->getManager()
                ->getRepository('IntaroBookStoreBundle:Book')
                ->find($id);

            if (!$book) {
                $data['error'] = 'Книга не найдена';

                $jsonContent = $serializer->serialize($data, 'json');

                return new Response($jsonContent);
            }

            if (!empty($requestData['name'])
                && !empty($requestData['author'])
                && !empty($requestData['readingDate'])
                && isset($requestData['download'])
            ) {
                $book->setName($requestData['name']);
                $book->setAuthor($requestData['author']);
                $book->setReadingDate(new \DateTime($requestData['readingDate']));
                $book->setDownload($requestData['download']);
            } else {
                $data['error'] = 'Заданы не все необходимые поля';

                $jsonContent = $serializer->serialize($data, 'json');

                return new Response($jsonContent);
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($book);
            $em->flush();

            $data = array(
                'success' => true,
                'message' => 'Книга успешно обновлена'
            );
        }

        $jsonContent = $serializer->serialize($data, 'json');

        return new Response($jsonContent);
    }

    private function checkApiKey($apiKey)
    {
        $this->container->getParameter('api_key');

        return $this->container->getParameter('api_key') == $apiKey;
    }
}
