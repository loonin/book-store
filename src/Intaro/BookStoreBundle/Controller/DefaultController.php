<?php

namespace Intaro\BookStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('IntaroBookStoreBundle:Default:index.html.twig', array('name' => $name));
    }

    public function showAction()
    {
        return $this->render('IntaroBookStoreBundle:Show:index.html.twig');
    }

}
