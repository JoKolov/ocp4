<?php

namespace JNi\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JNiDefaultBundle:Home:index.html.twig');
    }
}
