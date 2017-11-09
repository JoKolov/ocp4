<?php

namespace JNi\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JNiTicketingBundle:Default:index.html.twig');
    }
}
