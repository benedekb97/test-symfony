<?php


namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $passwordEncoder;
    private $security;
    private $urlGenerator;
    private $authenticationManager;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Security $security, UrlGeneratorInterface $urlGenerator, AuthenticationManagerInterface $authenticationManager)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->authenticationManager = $authenticationManager;
    }

    public function supports(Request $request)
    {

        return 'login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'captcha' => $request->request->get('captcha')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->em->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $login_attempts = $user->getLoginAttempts();
        if($login_attempts>2){
            $captcha = $credentials['captcha'];// check captcha;
        }

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $request->request->get('_username')]);

        if($user){
            $user->setLoginAttempts(0);
            $this->em->persist($user);
            $this->em->flush();
        }

        return new RedirectResponse($this->urlGenerator->generate('admin'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $username = $request->request->get('_username');
        return new RedirectResponse($this->urlGenerator->generate('failed_login', ['username' => $username]));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if($authException){
            return null;
        }
    }

    public function supportsRememberMe()
    {
        // TODO: Implement supportsRememberMe() method.
    }

}