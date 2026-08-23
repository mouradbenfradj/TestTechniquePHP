<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaulController extends AbstractController
{
    #[Route('/', name: 'app_defaul')]
    public function index(): Response
    {
        return $this->render('defaul/index.html.twig', [
            'controller_name' => 'DefaulController',
        ]);
    }
}
