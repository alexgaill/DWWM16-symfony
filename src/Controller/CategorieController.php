<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/categorie/save', name: "categorie_save")]
    public function add(ManagerRegistry $manager, Request $request): Response
    {
        // On créé un nouvel objet catégorie
        $categorie = new Categorie;
        // $categorie->setName("Catégorie n° 1");
        // On appelle la méthode createFormBuilder qui permet de générer un formulaire
        // $form = $this->createFormBuilder($categorie)
        //     // On ajoute des champs au formulaire
        //     ->add('name', TextType::class, [
        //         'label' => "Nom de la catégorie"
        //     ])
        //     ->add('submit', SubmitType::class, [
        //         'label' => "Ajouter catégorie",
        //         'attr' => ['class' => 'btn btn-primary']
        //     ])
        //     ->getForm();

        // On génère le formulaire avec la commande php bin/console make:form
        $form = $this->createForm(CategorieType::class, $categorie);
        // On associe les informations obtenues en POST au formulaire
        $form->handleRequest($request);
        // On vérifie que le formulaire est bien soumis et que les données sont valides
        // Si c'est le cas, on enregistre en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($categorie);
            $om->flush();
            return $this->redirectToRoute('categorie');
        }
        // Pour utiliser le formulaire, on doit l'envoyer à la vue sous forme de string
        // On utilise donc la méthode createView()
        return $this->render("categorie/save.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * Pour récupérer un élément en fonction de son id, on précise dans la route le paramètre récupéré dans l'url ({id})
     * Ce paramètre doit ensuite être récupéré en paramètre de la méthode (on le place entre les parenthèses)
     * Il ne nous reste qu'à utiliser la méthode find du repository pour récupérer la catégorie en fonction de son id
     */
    #[Route('/categorie/{id}', name: 'categorie_single', methods: ["GET"], requirements: ['id' => '\d+'])]
    public function single(int $id, ManagerRegistry $manager): Response
    {
        return $this->render('categorie/single.html.twig', [
            'categorie' => $manager->getRepository(Categorie::class)->find($id)
        ]);
    }


    #[Route("/categorie/{id}/update", name:"categorie_update", methods: ["GET", "POST"], requirements:['id' => "\d+"])]
    public function update (Categorie $categorie, Request $request, ManagerRegistry $manager):Response
    {
        // Symfony peut récupérer la catégorie directement en fonction de l'id en paramètre de la méthode du controller

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('categorie_single', ['id' => $categorie->getId()]);
        }

        return $this->render("categorie/update.html.twig", [
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }

    #[Route("/categorie/{id}/delete", name:"categorie_delete")]
    public function delete(Categorie $categorie, ManagerRegistry $manager): Response
    {
        // On appelle le manager et on utilise la méthode remove qui ajoute l'élément à supprimer à la queue des exécutions SQL
        $em = $manager->getManager();
        $em->remove($categorie);
        $em->flush();
        $this->addFlash('success', "La catégorie '".$categorie->getName()."' a bien été supprimée");
        return $this->redirectToRoute('categorie');
    }
}

/**
 * Refaire les méthodes update et delete pour article
 * Ajouter des messages flash de confirmation d'exécution
 */