<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route("/article/save", name:"article_save", methods: ["POST", "GET"])]
    public function add(Request $request, ManagerRegistry $manager):Response
    {
        $article = new Article;

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $manager->getManager();
                $article->setCreatedAt(new \DateTime());
                $em->persist($article);
                $em->flush();
                $this->addFlash('success', "L'article a bien été enregistré");
                return $this->redirectToRoute('article_single', ['id' => $article->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', "Une erreur s'est produite, réessayez!");
                $this->addFlash('danger', $e->getMessage());
            }
        }
        
        return $this->render('article/save.html.twig', [
            'form' => $form->createView()
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

    #[Route("/article/{id}/update", name:"article_update", methods:["POST", "GET"], requirements: ['id' => "\d+"])]
    public function update(Article $article, Request $request, ManagerRegistry $manager):Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', "La catégorie a bien été modifiée");
            return $this->redirectToRoute('article_single', ['id' => $article->getId()]);
        }

        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route("/article/{id}/delete", name:"article_delete", methods:["GET"], requirements:['id' => "\d+"])]
    public function delete (Article $article, ManagerRegistry $manager):Response
    {
        $em = $manager->getManager();
        $em->remove($article);
        $em->flush();
        $this->addFlash("success", "L'article ". $article->getTitle(). " a bien été supprimé!");
        return $this->redirectToRoute('article');
    }
}
