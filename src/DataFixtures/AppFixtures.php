<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\Article;
use Cocur\Slugify\Slugify;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        

        $faker = Factory::create('fr-FR'); // faker en français
        $utilisateurs = [];

        // Génération de mon compte

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $administrateur = new Utilisateur();
        $adminpassword = $this->encoder->encodePassword($administrateur, 'password');

        $administrateur->setNom('Midlaire')
                    ->setPrenom('Vincent')
                    ->setPays('Belgique')
                    ->setVille('Ecaussinnes')
                    ->setRue('Rue des plantes')
                    ->setNumerorue(mt_rand(1,150))
                    ->setEmail('vincent.midlaire@pepiniere.be')
                    ->setHash($adminpassword)
                    ->addUtilisateurRole($adminRole)
                    ;
            
        $manager->persist($administrateur);

        $utilisateurs[] = $administrateur;

        // Génération des utilisateurs

        for ($i=0; $i < 15; $i++) { 
            $utilisateur = new Utilisateur();

            $nom = $faker->lastname();
            $prenom = $faker->firstname();
            $utilisateurpassword = $this->encoder->encodePassword($utilisateur, 'password');

            $utilisateur->setNom($nom)
                        ->setPrenom($prenom)
                        ->setPays($faker->country())
                        ->setVille($faker->city())
                        ->setRue($faker->streetName())
                        ->setNumerorue(mt_rand(1,150))
                        ->setEmail(strtolower($prenom . '.' . $nom . '@pepiniere.be'))
                        ->setHash($utilisateurpassword);
            
            $manager->persist($utilisateur);

            $utilisateurs[] = $utilisateur;
        }


        // Génération des articles

        $nomArticle = ['Citronnier', 'Erable du Japon', 'Olivier', 'Yucca', 'Palmier', 'Glycine Blanche', 'Cerisier Nain', 'Arbre à Papillons', 'ChèvreFeuille', 'Passiflore'];
        $slugify = new Slugify();
        $articles = [];

        for ($i=0; $i < 10; $i++) { 
            
            $article = new Article();
            $descriptionArticle = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $resumeArticle = $faker->sentence();
            $slug = $slugify->slugify($nomArticle[$i]);

            $article->setNom($nomArticle[$i])
                ->setPrix(mt_rand(100,400))
                ->setDescription($descriptionArticle)
                ->setQuantite(mt_rand(0,100))
                ->setResume($resumeArticle)
                ->setSlug($slug);    
            
            $manager->persist($article);

            $articles[] = $article;
        }

        // Génération des commentaires

        for ($i=0; $i < 16; $i++) { 
            
            if (mt_rand(0,1)) {
                for ($j=0; $j < mt_rand(1,4); $j++) { 
                    $commentaire = new Commentaire();
                    
                    $commentaire->setContenu($faker->paragraph(2))
                                ->setNote(mt_rand(1,5))
                                ->setDate($faker->dateTimeBetween('-6 months'))
                                ->setArticle($articles[mt_rand(0,9)])
                                ->setAuteur($utilisateurs[$i]);

                                $manager->persist($commentaire);
                }
            }
        }

        $manager->flush();
    }
}
