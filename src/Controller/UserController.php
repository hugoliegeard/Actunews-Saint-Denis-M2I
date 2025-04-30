<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/inscription', name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request,
                             UserPasswordHasherInterface $hasher,
                             EntityManagerInterface $entityManager): Response
    {

        # Création d'un nouvel utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        # Récupérer le formulaire
        # 1er paramètre : Le type de formulaire
        # 2ème paramètre : L'entité à laquelle le formulaire est lié
        # ($user : L'objet qui contiendra les données du formulaire )
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        # Vérifier si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {

            # Hashage du mot de passe
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));

            # Sauvegarde dans la base de donnée
            $entityManager->persist($user);
            $entityManager->flush();
            
            # TODO : Envoi d'un email de confirmation / vérification

            # Notification de succès
            $this->addFlash('success', 'Votre compte a bien été créé !');

            # Redirection vers la page de connexion
            return $this->redirectToRoute('security_login');
        }

        # Afficher le formulaire
        return $this->render('user/register.html.twig', [
            'form' => $form
        ]);

    }
}