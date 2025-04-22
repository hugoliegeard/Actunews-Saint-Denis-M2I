<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_home',  methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('default/home.html.twig');
        # return new Response('<h1>Accueil</h1>');
    }

    #[Route('/categorie/{slug}', name: 'default_category', methods: ['GET'])]
    # https://localhost:8000/politique
    public function category($slug): Response
    {
        return $this->render('default/category.html.twig');
        # return new Response("<h1>Catégorie : $slug</h1>");
    }

    #[Route('/{categorySlug}/{postSlug}_{id}.html', name: 'default_post', methods: ['GET'])]
    # https://localhost:8000/politique/bientot-de-nouvelles-elections-municipales_45768.html
    public function post($categorySlug, $postSlug, $id): Response
    {
        # return $this->render('default/post.html.twig');
        return new Response("<h1>Article : $categorySlug/$postSlug/$id</h1>");
    }
}
