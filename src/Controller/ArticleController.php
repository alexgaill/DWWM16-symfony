<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article')]
    public function index(ArticleRepository $repository): Response
    {
        // Charger le repository en paramètre de la fonction est le fonctionnement que l'on a en Symfony 4
        return $this->render('article/index.html.twig', [
            'articlesList' => $repository->findAll()
        ]);
    }
}
