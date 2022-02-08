<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'post')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller irvin!',
            'path' => 'src/Controller/PostController.php',
        ]);
    }
    #[Route('/post/list', name: 'post-list')]
    public function list(): Response
    {
        return $this->json([
            'message' => 'Soy una lista',
            'path' => 'src/Controller/PostController.php',
        ]);
    }
}
