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
     * Pour récupérer un élément en fonction de son id, on précise dans la route le paramètre récupéré dans l'url ({id})
     * Ce paramètre doit ensuite être récupéré en paramètre de la méthode (on le place entre les parenthèses)
     * Il ne nous reste qu'à utiliser la méthode find du repository pour récupérer la catégorie en fonction de son id
     */
    #[Route('/categorie/{id}', name: 'categorie_single')]
    public function single(int $id, ManagerRegistry $manager):Response
    {
        return $this->render('categorie/single.html.twig', [
            'categorie' => $manager->getRepository(Categorie::class)->find($id)
        ]);
    }

}
