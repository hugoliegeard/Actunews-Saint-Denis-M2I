<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    #[Route('/rediger-un-article', name: 'post_create', methods: ['GET', 'POST'])]
    public function createPost(Request                $request,
                               SluggerInterface       $slugger,
                               #[Autowire('%kernel.project_dir%/public/uploads/posts')] string $postsDirectory,
                               EntityManagerInterface $entityManager): Response
    {
        # Création d'un nouvel article
        $post = new Post();
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setUpdatedAt(new \DateTimeImmutable());

        # Récupérer le formulaire
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        # Vérifier si le formulaire est soumis
        if ($form->isSubmitted()) {

            # Par sécurité
            $post->setSlug($slugger->slug($post->getSlug()));

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = $post->getSlug() . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($postsDirectory, $newFilename);
                } catch (FileException $e) {
                }

                $post->setImage($newFilename);
            }

            # Insertion de l'article dans la base
            $entityManager->persist($post);
            $entityManager->flush();

            # Redirection vers la page de l'article
            return $this->redirectToRoute('default_post', [
                'categorySlug' => $post->getCategory()->getSlug(),
                'postSlug' => $post->getSlug(),
                'id' => $post->getId()
            ]);

        }

        # Affichage du formulaire
        return $this->render('post/create.html.twig', [
            'form' => $form
        ]);

    }
}