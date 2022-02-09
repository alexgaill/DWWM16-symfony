<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Exercice Créer l'ArticleController
 * Dans la méthode index, récupérer tous les articles et les envoyer vers un template
 * Dans le template, faire un tableau affichant les id, title, createdAt de chaque article avec en plus un morceau du content (max 50 caractères)
 */
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
    #[Route("/article/{id}", name: 'article_single')]
    public function single (int $id, ManagerRegistry $manager):Response
    {
        $article = $manager->getRepository(Article::class)->find($id);

        return $this->render("article/single.html.twig", [
            'article' => $article
        ]);
    }
}
