<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * Passer une class en paramètre d'une méthode utilise un design pattern appelé l'injection de dépendance
     *
     * @param ManagerRegistry $manager
     * @return Response
     */
    #[Route('/categorie', name: 'categorie')]
    public function index(ManagerRegistry $manager): Response
    {
        // On charge en paramètre l'interface ManagerRegistry qui nous permet de charger le repository dédié aux catégories
        // On utilise ensuite la méthode findAll() qui permet de récupérer toutes les catégories présentes en BDD
        // Cet appel au repository est l'appel à favoriser
        $categories = $manager->getRepository(Categorie::class)->findAll();
        // dump($categories); // Permet d'obtenir les informations dans la barre de debug
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Exercice Créer l'ArticleController
     * Dans la méthode index, récupérer tous les articles et les envoyer vers un template
     * Dans le template, faire un tableau affichant les id, title, createdAt de chaque article avec en plus un morceau du content (max 50 caractères)
     */
}
