<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\AjoutPanierType;
use App\Form\CommentaireType;
use App\Repository\PanierRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles_index")
     */
    public function index(ArticleRepository $repo)
    {

        $articles = $repo->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    /**
     * Permet d'afficher un seul article
     *
     * @Route("/articles/{id}", name="article_show")
     * 
     * @param Article $article
     * @return Response
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager, PanierRepository $repoPanier){

        $nouveauPanier = new Panier();

        $form = $this->createForm(AjoutPanierType::class, $nouveauPanier);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $this->getUser();
            $paniers = $repoPanier->findByUtilisateur($utilisateur);

            $nouveauPanier->setUtilisateur($utilisateur)
            ->setArticle($article)
            ;

            foreach ($paniers as $panier) {

                if ($panier->getArticle() == $nouveauPanier->getArticle()) {
                    $this->addFlash(
                        'danger',
                        'Vous avez déjà ajouté cet article à votre panier'
                        );
                
                    return $this->redirectToRoute('panier_global');
                }
            }

            $manager->persist($nouveauPanier);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre article a bien été ajouté à votre panier'
                );
        
            return $this->redirectToRoute('panier_global');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Permet d'écrire un commentaire sous un article
     *
     * @Route("/articles/{id}/commentaire", name="article_commentaire")
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function commentaire(Article $article, Request $request, EntityManagerInterface $manager){

        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);


        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $utilisateur = $this->getUser();

            $commentaire->setArticle($article)
                        ->setAuteur($utilisateur)
                        ;

            $manager->persist($commentaire);
            $manager->flush();
        
            $this->addFlash(
                'success',
                'Votre commentaire a bien été ajouté !'
                );
        
            return $this->redirectToRoute('home');
        }

        return $this->render('article/commentaire.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
