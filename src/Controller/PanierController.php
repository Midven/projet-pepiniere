<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Commande;
use App\Repository\PanierRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    /**
     * Affiche le panier de l'utilisateur
     * 
     * @Route("/panier", name="panier_global")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('panier/index.html.twig');
    }
    
    /**
     * Permet de supprimer un élément du panier
     * 
     * @Route("/panier/{id}/supprimer", name="panier_suppression_article")
     * @IsGranted("ROLE_USER")
     * 
     * @param Panier $panier
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function deleteElementPanier(Panier $panier, EntityManagerInterface $manager){
        $manager->remove($panier);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'article a  bien été supprimé de votre panier"
        );

        return $this->redirectToRoute("panier_global");
    }

    /**
     * Supprimer tout le panier
     *
     * @Route("/panier/supprimer", name="panier_suppression_totale")
     * @IsGranted("ROLE_USER")
     * 
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function deleteAllPanier(EntityManagerInterface $manager, PanierRepository $repo){
        $utilisateur = $this->getUser();

        $paniers = $repo->findByUtilisateur($utilisateur);

        foreach ($paniers as $panier) {
            $manager->remove($panier);
        }
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre panier à bien été vidé"
        );

        return $this->redirectToRoute("panier_global");
    }

    /**
     * Valider la commande
     *
     * @Route("/panier/commander", name="panier_commander")
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function passerCommande(EntityManagerInterface $manager, PanierRepository $repoPanier, ArticleRepository $repoArticle){
        
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $dateToString = date('Y-m-d H:i:s');
        $utilisateur = $this->getUser();

        $hash = $utilisateur->getId() . Sha1($dateToString);
        
        $paniers = $repoPanier->findByUtilisateur($utilisateur);
        $articles = $repoArticle->findAll();

        foreach ($paniers as $panier) {

            foreach ($articles as $article) {
                if ($panier->getArticle() == $article) {
                    $quantitePanier = $panier->getQuantite();
                    $quantiteArticle = $article->getQuantite();
                    $nouvelleQuantite = $quantiteArticle - $quantitePanier;
                    if ($nouvelleQuantite < 0) {
                        $this->addFlash(
                            'danger',
                            "Certains de vos articles ne sont plus disponibles ... Veuillez vérifier que la quantité commandée n'excède pas la quantité disponible."
                        );
                
                        return $this->redirectToRoute("panier_global");
                    }else{
                        $article->setQuantite($nouvelleQuantite);

                        $commande = new Commande();

                        $commande->setUtilisateur($utilisateur)
                                ->setDate($date)
                                ->setQuantite($quantitePanier)
                                ->setCommandeid($hash)
                                ->setArticle($article)
                                ;
                        $manager->persist($commande);

                        $manager->remove($panier);
                    }
                }
            }
        }
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre commande a bien été effectuée"
        );

        return $this->redirectToRoute("panier_global");
        
    }
}
