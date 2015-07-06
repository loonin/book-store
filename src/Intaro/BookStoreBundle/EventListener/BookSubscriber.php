<?php

namespace Intaro\BookStoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Intaro\BookStoreBundle\Entity\Book;

class BookSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postRemove',
        );
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $book = $args->getEntity();

        if ($book instanceof Book) {
            $file = $book->getAbsolutePath('file');
            $cover = $book->getAbsolutePath('cover');

            if ($file && file_exists($file)) {
                unlink($file);
            }

            if ($cover && file_exists($cover)) {
                unlink($cover);
            }
        }
    }
}