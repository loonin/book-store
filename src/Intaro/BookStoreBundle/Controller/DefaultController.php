<?php

namespace Intaro\BookStoreBundle\Controller;

use Intaro\BookStoreBundle\Entity\Book;
use Intaro\BookStoreBundle\Form\Type\BookType;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $memCache = $this->get('memcache.default');
        $books = $memCache->get('books');
        if (!$books) {
            $books = $this->getDoctrine()
                ->getRepository('IntaroBookStoreBundle:Book')
                ->findAllOrderedByReadingDate();

            $memCache->set('books', $books, false, $this->container->getParameter('cache_time'));
        }

        return $this->render('IntaroBookStoreBundle:Default:index.html.twig', array('books' => $books));
    }

    public function showAction()
    {
        return $this->render('IntaroBookStoreBundle:Show:index.html.twig');
    }


    public function createAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(new BookType(), $book);

        if ($request->getMethod() == 'POST') {
            if (!$form['download']->getData()) {
                $book->setDownload(0);
            }

            $form->submit($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($book);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Новая книга добавлена.'
                );

                $memCache = $this->get('memcache.default');

                if ($memCache->get('books')) {
                    $memCache->set('books');
                }

                return $this->redirect($this->generateUrl('book_store_index'));
            }
        }

        return $this->render('IntaroBookStoreBundle:Default:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository('IntaroBookStoreBundle:Book')->find($id);

        $form = $this->createForm(new BookType(), $book);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if (!$book->getCover() && $book->getCoverSrc()) {
                $book->setCover('update');
            }

            if (!$book->getFile() && $book->getFileSrc()) {
                $book->setFile('update');
            }

            if ($form->isValid()) {
                $book->setDownload($book->getDownload());

                $em->flush();

                $this->addFlash(
                    'notice',
                    'Книга отредактирована.'
                );

                $memCache = $this->get('memcache.default');

                if ($memCache->get('books')) {
                    $memCache->set('books');
                }

                return $this->redirect($this->generateUrl('book_store_index'));
            }
        }

        return $this->render('IntaroBookStoreBundle:Default:edit.html.twig', array(
            'form' => $form->createView(),
            'book' => $book
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('IntaroBookStoreBundle:Book')->find($id);

        $em->remove($book);
        $em->flush();

        $this->addFlash(
            'notice',
            'Книга удалена.'
        );

        $memCache = $this->get('memcache.default');

        if ($memCache->get('books')) {
            $memCache->set('books');
        }

        return $this->redirect($this->generateUrl('book_store_index'));
    }

    public function deleteFileAction($type, $id)
    {
        $serializer = SerializerBuilder::create()->build();

        if ($type != 'cover' && $type != 'file') {
            $data = [
                'success' => false,
                'message' => 'Неверный тип'
            ];
            $jsonContent = $serializer->serialize($data, 'json');

            return new Response($jsonContent);
        }

        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('IntaroBookStoreBundle:Book')->find($id);

        if (!$book) {
            $data = [
                'success' => false,
                'message' => 'Не найдена книга с id: ' . $id
            ];
            $jsonContent = $serializer->serialize($data, 'json');

            return new Response($jsonContent);
        }

        $book->removeFile($type);
        $em->flush();

        $data = [
            'success' => true,
            'message' => 'Объект' . $type . ' для книги с id: ' . $id . ' удалён'
        ];
        $jsonContent = $serializer->serialize($data, 'json');

        $memCache = $this->get('memcache.default');

        if ($memCache->get('books')) {
            $memCache->set('books');
        }

        return new Response($jsonContent);
    }
}
