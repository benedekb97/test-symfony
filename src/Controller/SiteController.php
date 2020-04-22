<?php

// src/Controller/SiteController.php
namespace App\Controller;

use \Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        $roles = [
            'ROLE_ADMIN' => 'Adminisztrátor',
            'ROLE_AUTHENTICATED' => 'Bejelentkezett felhasználó',
            'ROLE_EDITOR' => 'Tartalomszerkesztő',
            'ROLE_USER' => 'Felhasználó'
        ];
        return $this->render('admin.html.twig', [
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/editor", name="editor")
     */
    public function editor(): Response
    {
        return $this->render('editor.html.twig');
    }

    public function user(): Response
    {
        return $this->render('user.html.twig');
    }
}