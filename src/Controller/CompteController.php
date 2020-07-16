<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\Commande;
use App\Form\AvatarType;
use App\Form\CompteType;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Form\CommentaireType;
use App\Form\InscriptionType;
use App\Entity\ModificationPassword;
use App\Repository\AvatarRepository;
use Symfony\Component\Form\FormError;
use App\Form\ModificationPasswordType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompteController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="compte_connexion")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('compte/connexion.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @Route("/logout", name="compte_deconnexion")
     * @return void
     */
    public function logout(){
        // symfony gère le logout
        // il faut ajouter des trucs dans le security.yaml
    }

    /**
     * Permet de gérer l'inscription de l'utilisateur
     *
     * @Route("/inscription", name="compte_inscription")
     * 
     * @return Response
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $utilisateur = new Utilisateur();

        $form = $this->createForm(InscriptionType::class, $utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($utilisateur, $utilisateur->getHash());
            $utilisateur->setHash($hash);

            $manager->persist($utilisateur);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé, vous pouvez maintenant vous connecter !'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('compte/inscription.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Modification des données du compte
     * 
     * @Route("/moncompte", name="compte_modification")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function modification(Request $request, EntityManagerInterface $manager) {

        $utilisateur = $this->getUser();

        $form = $this->createForm(CompteType::class, $utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($utilisateur); // pas necessaire de persister une entité qui existe déjà
            $manager->flush();

            $this->addFlash(
                'success',
                'Les données du profil on été enregistrées avec succès'
            );
        }

        return $this->render('compte/modification.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Modification du mot de passe par l'utilisateur
     *
     * @Route("/moncompte/motdepasse", name="compte_modification_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function modificationPassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){


        $modificationpassword = new ModificationPassword();

        $form = $this->createForm(ModificationPasswordType::class, $modificationpassword);

        $utilisateur = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le ancienpassword du formulaire soit le même que le password de l'user
            if (!password_verify($modificationpassword->getAncienpassword(), $utilisateur->getHash())) {
                // Gérér l'erreur
                $form->get('ancienpassword')->addError(new FormError('Votre ancien mot de passe n\'est pas correct')); 
                // récupère le champ ancienpassword et lui ajoute une erreur
            }
            else{
                $nouveaupassword = $modificationpassword->getNouveaupassword();
                $hash = $encoder->encodePassword($utilisateur, $nouveaupassword);
                
                $utilisateur->setHash($hash);

                $manager->persist($utilisateur);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe à bien été modifié !'
                );

                return $this->redirectToRoute('home');
            }
        }


        return $this->render('compte/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Vue des commentaires de l'utilisateur connecté
     *
     * @Route("/moncompte/mescommentaires", name="compte_mescommentaires")
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function mesCommentaires(){
        $utilisateur = $this->getUser();

        return $this->render('compte/mescommentaires.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }


    /**
     * Permet de modifier un commentaire
     * 
     * @Route("/moncompte/mescommentaires/{id}/edit", name="compte_mescommentaires_edit")
     * @IsGranted("ROLE_USER")
     *
     * @param Commentaire $commentaire
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function editCommentaire(Commentaire $commentaire, EntityManagerInterface $manager, Request $request){
        $utilisateur = $this->getUser();

        $form = $this->createForm(CommentaireType::class, $commentaire);

        // handleRequest -> gère la requête
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if($commentaire->getAuteur() == $utilisateur){
                $manager->persist($commentaire);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre commentaire à bien été modifié !"
                );
                return $this->redirectToRoute("compte_mescommentaires");
            }else{
                $this->addFlash(
                    'danger',
                    "Vous ne pouvez pas modifier un commentaire qui n'est pas le votre !"
                );
                return $this->redirectToRoute("compte_mescommentaires");
            }
        }

        return $this->render('compte/editcommentaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/moncompte/mescommentaires/{id}/supprimer", name="compte_mescommentaires_delete")
     * @IsGranted("ROLE_USER")
     * 
     * @param Commentaire $commentaire
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function deleteCommentaire(Commentaire $commentaire, EntityManagerInterface $manager){
        $utilisateur = $this->getUser();

        if($commentaire->getAuteur() == $utilisateur){
            $manager->remove($commentaire);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire à bien été supprimé"
            );
            return $this->redirectToRoute("compte_mescommentaires");
        }else{
            $this->addFlash(
                'danger',
                "Vous ne pouvez pas supprimer un commentaire qui n'est pas le votre !"
            );
            return $this->redirectToRoute("compte_mescommentaires");
        }
    }


    /**
     * Vues des commandes de l'utilisateur connecté
     *
     * @Route("/moncompte/mescommandes", name="compte_mescommandes")
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function mesCommandes(CommandeRepository $repoCommande){
        $utilisateur = $this->getUser();
        $commandes = $repoCommande->findByUtilisateur($utilisateur, ['date' => 'DESC' ]);

        return $this->render('compte/mescommandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    
    /**
     * Permet à l'utilisateur de modifier son avatar
     * 
     * @Route("/moncompte/avatar", name="compte_avatar")
     * @IsGranted("ROLE_USER")
     *
     * @return void
     */
    public function Avatar(AvatarRepository $repoAvatar, EntityManagerInterface $manager, Request $request){
        
        $utilisateur = $this->getUser();
        
        if ($utilisateur->getAvatar() == true) {
            // l'entité avatar actuelle correspondant à l'utilisateur
            $avatarUtilisateur = $repoAvatar->findOneByUtilisateur($utilisateur);
            // la propriété avatar dans l'Utilisateur
            // $avatarUtilisateurLien = $utilisateur->getAvatar();

            $nouveauAvatar = new Avatar();

            $form = $this->createForm(AvatarType::class, $nouveauAvatar);
            
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){
                $manager->remove($avatarUtilisateur);
                // $manager->remove($avatarUtilisateurLien);
                $manager->flush();
                
                $utilisateur->setAvatar($nouveauAvatar);
                
                $nouveauAvatar->setUtilisateur($utilisateur);

                // $manager->persist($utilisateur);
                
                $manager->persist($nouveauAvatar);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre avatar a été correctement modifié'
                );

                return $this->redirectToRoute('compte_modification');
            }
            
        }
        else{
            $nouveauAvatar = new Avatar();
            $form = $this->createForm(AvatarType::class, $nouveauAvatar);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $nouveauAvatar->setUtilisateur($utilisateur);

                $manager->persist($nouveauAvatar);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre avatar a été correctement modifié'
                );

                return $this->redirectToRoute('compte_modification');

            }
        }

        return $this->render('compte/avatar.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
