<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Exercice Créer l'ArticleController
 * Dans la méthode index, récupérer tous les articles et les envoyer vers un template
 * Dans le template, faire un tableau affichant les id, title, createdAt de chaque article avec en plus un morceau du content (max 50 caractères)
 */
class ArticleController extends AbstractController
{
    #[Route(path:'/', name:'accueil')]
    public function accueil (ManagerRegistry $manager):Response
    {
        return $this->render('article/accueil.html.twig', [
            'articleList' => $manager->getRepository(Article::class)->findVoluptas()
        ]);
    }

    #[Route('/article', name: 'article')]
    public function index(ArticleRepository $repository): Response
    {
        // Charger le repository en paramètre de la fonction est le fonctionnement que l'on a en Symfony 4
        return $this->render('article/index.html.twig', [
            'articlesList' => $repository->findAll()
        ]);
    }

    #[
        Route("/article/save", name:"article_save", methods: ["POST", "GET"]),
        IsGranted(data:"ROLE_ADMIN", message:"Vous n'avez pas les droits")
    ]
    public function add(Request $request, ManagerRegistry $manager):Response
    {
        $article = new Article;

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $manager->getManager();
                $article->setCreatedAt(new \DateTime());

                // Je récupère l'image
                $image = $article->getPicture();
                // On génère un nom unique pour les images
                // $image->guessExtension() permet de récupérer le mimeType d'un fichier pour le réutiliser
                $imageName = md5(uniqid()) .'.'. $image->guessExtension();
                // move permet de déplacer une image dans un dossier.
                // Elle prend 2 paramètres:
                //  - Le dossier dans lequel on enregistre l'image
                //  - Le nom de l'image
                try {
                    $image->move(
                        $this->getParameter('upload_files'),
                        $imageName
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', "Une erreur s'est produite, réessayez!");
                    $this->addFlash('danger', $e->getMessage());
                }
                $article->setPicture($imageName);

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
        if(!$this->isGranted("ROLE_ADMIN")){
            $this->addFlash("danger", "Vous n'avez pas les droits pour modifier un article!");
            return $this->redirectToRoute("article");
        }
        if($article->getPicture()){
            $article->setPicture(
                new File($this->getParameter('upload_files').'/'. $article->getPicture())
            );
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $article->getPicture();
            $imageName = md5(uniqid()) .'.'. $image->guessExtension();
            
            try {
                $image->move(
                    $this->getParameter('upload_files'),
                    $imageName
                );
            } catch (FileException $e) {
                $this->addFlash('danger', "Une erreur s'est produite, réessayez!");
                $this->addFlash('danger', $e->getMessage());
            }
            $article->setPicture($imageName);

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
