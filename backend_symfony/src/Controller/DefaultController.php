<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/{vueRouting}", name="app_homepage", requirements={"vueRouting"=".*"})
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}