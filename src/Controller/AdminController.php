<?php

namespace App\Controller;

use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * Permet d'accéder au panneau d'administration
     * 
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * Permet d'afficher les commentaires
     *
     * @Route("/admin/commentaires", name="admin_commentaires")
     * @IsGranted("ROLE_ADMIN")
     * 
     * @return void
     */
    public function commentaireAdmin(CommentaireRepository $repoCommentaire){
        
        $commentaires = $repoCommentaire->findBy([], ['date' => 'DESC']);

        return $this->render('admin/commentaires.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }
    
    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/admin/commentaires/{id}/delete", name="admin_commentaires_delete")
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Commentaire $commentaire
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function commentaireAdminDelete(Commentaire $commentaire, EntityManagerInterface $manager){
        $manager->remove($commentaire);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$commentaire->getAuteur()->getNom()} {$commentaire->getAuteur()->getPrenom()} a bien été supprimé"
        );

        return $this->redirectToRoute('admin_commentaires');
    }
}
