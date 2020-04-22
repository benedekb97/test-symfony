<?php

// src/Controller/SiteController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
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
        if($this->isGranted('ROLE_AUTHENTICATED') || $this->isGranted('ROLE_EDITOR')) {
            return new RedirectResponse($this->generateUrl('admin'));
        }
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        if(!$this->isGranted('ROLE_EDITOR') && !$this->isGranted('ROLE_AUTHENTICATED')){
            $this->denyAccessUnlessGranted('');
        }
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
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        return $this->render('editor.html.twig');
    }

    /**
     * @Route("/user", name="user")
     */
    public function user(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AUTHENTICATED');
        return $this->render('user.html.twig');
    }
}