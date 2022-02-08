<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'categorie')]
    public function index(): Response
    {
        $categories = [
            [
                "name" => "PHP"
            ],
            [
                "name" => "Javascript"
            ],
            [
                "name" => "HTML"
            ],
            [
                "name" => "CSS"
            ],
        ];
        // dump($categories); // Permet d'obtenir les informations dans la barre de debug
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
