<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        if($request->isMethod('POST')) {

        }

        $form = $this->createFormBuilder()
            ->add('username', TextType::class)
            ->add('password', PasswordType::class);



        return $this->render('login.html.twig');
    }

    /**
     * @Route("/failed_login/{username}", name="failed_login")
     */
    public function failed(Request $request, $username = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if($user){
            $user->setLoginAttempts($user->getLoginAttempts() + 1);
            $em->persist($user);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('login', ['captcha' => 1]));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }
}
