<?php

namespace App\Controller;

use App\Entity\User;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        if($this->getUser()) {
            return new RedirectResponse($this->generateUrl('admin'));
        }

        if($request->isMethod('POST')) {
            $user_data = $request->request->get('user');

            $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['username' => $request->request->get('user')['username']]);

            if(!$user) {
                return new RedirectResponse($this->generateUrl('login'));
            }

            $form = $this->createForm(\App\Form\Type\User::class);

            if($user && $user->getLoginAttempts()>2) {
                $form->add('captcha', CaptchaType::class);
            }

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid() && $passwordEncoder->isPasswordValid($user, $user_data['password'])) {
                $user->setLoginAttempts(0);
                $user->setLastLogin(new \DateTime('now'));
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();


                $token = new UsernamePasswordToken($user, ['username' => $user->getUsername(), 'password' => $user->getPassword()], 'main', $user->getRoles());

                $this->get('security.token_storage')->setToken($token);


                return new RedirectResponse($this->generateUrl('admin'));
            }

            if($user){
                $user->setLoginAttempts($user->getLoginAttempts() + 1);
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
            }
        }else{
            $form = $this->createForm(\App\Form\Type\User::class);
        }

        $login_attempts = 0;
        if(isset($user)){
            $login_attempts = $user->getLoginAttempts();
        }

        if($login_attempts>2 && !$form->isSubmitted()){
            $form->add('captcha', CaptchaType::class);
        }

        $form = $form->createView();

        return $this->render('login.html.twig',[
            'form' => $form
        ]);
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
