<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/home')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index():Response{
        return $this->render('home/home.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact():Response{
        return $this->render('home/contact.html.twig');
    }
}
