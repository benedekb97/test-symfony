<?php

namespace App\Controller;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\DateTime;

class SecurityController extends AbstractController
{
     /**
     * @Route("/update", name="update_last_login")
     */
    public function updateLogin()
    {
        $user = $this->getUser();
        if(!$user) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }
        $user->setLastLogin(new \DateTime('now'));
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse($this->generateUrl('admin'));
    }
}
