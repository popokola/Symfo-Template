<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\UserType;
use App\Form\PasswordResetType;
use App\Form\VerifyPasswordType;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use App\Service\JWTService;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route(path: '/register', name: 'app_register')]
    public function register(UserRepository $userRepository, Request $request, SendMailService $mailer, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            $payload = [
                'user_id' => $user->getId()           
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mailer->sendMail(
                'no-reply@vgcreator.fr',
                $user->getEmail(), 
                'Bienvenue sur le site VGCreator', 
                'activation-token',
                compact('user', 'token'),
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route(path: '/password-reset', name: 'app_password_reset')]
    public function passwordReset(Request $request, UserRepository $userRepository, SendMailService $mailer, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(PasswordResetType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneBy(['email' => $data->getEmail()]);
            if (!$user) {
                $this->addFlash('danger', 'Aucun compte n\'est associé à cette adresse email');
                return $this->redirectToRoute('app_password_reset');
            }

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            $payload = [
                'user_id' => $user->getId()           
            ];

            // token only valid for 45 minutes (2700 seconds) default is 3 hours (10800 seconds)
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'), 2700);
            $mailer->sendMail(
                'no-reply@vgcreator.fr',
                $user->getEmail(), 
                'VGCreator demande de réinitialisation de mot de passe', 
                'password-reset',
                compact('user', 'token'),
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password-reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route(path: '/password-reset/{token}', name: 'app_password_reset_token')]
    public function verifyPasswordReset(string $token, Request $request, UserRepository $userRepository, SendMailService $mailer, JWTService $jwt): Response
    {
      
        if (!$jwt->isValid($token) 
        || $jwt->isExpired($token) 
        || !$jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            $this->addFlash('danger', 'Le lien de réinitialisation de mot de passe est invalide ou a expiré');
            return $this->redirectToRoute('app_password_reset');
        }

        $user = new User();
        $payload = $jwt->getPayload($token);
        $user = $userRepository->find($payload['user_id']);
        if (!$user) {
            $this->addFlash('danger', 'Aucun compte n\'est associé à cette adresse email');
            return $this->redirectToRoute('app_password_reset');
        }

        $form = $this->createForm(VerifyPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPlainPassword($data->getPlainPassword());
            $user->setupdatedAT(new \DateTime());
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/password-recovery.html.twig', [
            'form' => $form->createView()
        ]);
       
    }


    #[Route(path: '/activation/{token}', name: 'app_activation')]
    public function activation(string $token, JWTService $jwt, UserRepository $userRepository): Response
    {
        if($jwt->isValid($token) 
        && !$jwt->isExpired($token) 
        && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);

            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $userRepository->save($user, true);
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('app_login');
            }
        }
    
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

}
