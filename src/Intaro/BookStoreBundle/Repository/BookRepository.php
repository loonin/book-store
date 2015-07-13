<?php

namespace Intaro\BookStoreBundle\Repository;

use Doctrine\ORM\EntityRepository;


class BookRepository extends EntityRepository
{
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT b FROM IntaroBookStoreBundle:Book b ORDER BY b.name ASC')
            ->getResult();
    }

    public function findAllOrderedByReadingDate()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT b FROM IntaroBookStoreBundle:Book b ORDER BY b.reading_date DESC')
            ->getResult();
    }
} 