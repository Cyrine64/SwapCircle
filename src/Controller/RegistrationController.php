<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginFormAuthAuthenticator;
use Psr\Log\LoggerInterface;
use Gregwar\Captcha\CaptchaBuilder;

class RegistrationController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthAuthenticator $authenticator,
        SessionInterface $session
    ): Response {
        if (!$session->isStarted()) {
            $session->start();
        }

        // if (!$session->has('gregwar_captcha')) {
        //     $this->generateCaptcha($session);
        // }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $user->setRoles(['ROLE_USER']);

        if ($form->isSubmitted()) {
            $this->logger->info('Form submitted.');

            // $captchaInput = $form->get('captcha')->getData();
            // $captchaStored = $session->get('gregwar_captcha');
       

            // $this->logger->info("User-entered CAPTCHA: $captchaInput");
            // $this->logger->info("Stored CAPTCHA: $captchaStored");

            // if ($captchaInput !== $captchaStored) {
            //     $this->logger->error("CAPTCHA mismatch. Registration failed.");
            //     $this->addFlash('error', 'Invalid CAPTCHA, please try again.');
            //     return $this->render('registration/register.html.twig', ['registrationForm' => $form]);
            // }

            if ($form->isValid()) {
                $this->logger->info('Form is valid, processing registration.');

                $plainPassword = $form->get('password')->getData();
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                // $session->remove('gregwar_captcha');

                return $userAuthenticator->authenticateUser($user, $authenticator, $request);
            } else {
                $this->logger->error("Form validation failed.");
            }
        }

        return $this->render('registration/register.html.twig', ['registrationForm' => $form]);
    }

    #[Route('/captcha-image', name: 'captcha_image')]
    public function captchaImage(SessionInterface $session): Response
    {
        $captcha = new CaptchaBuilder($session->get('gregwar_captcha'));
        $captcha->build();

        $response = new Response();
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->setContent($captcha->get());

        return $response;
    }

    #[Route('/register/captcha', name: 'captcha_refresh')]
    public function refreshCaptcha(SessionInterface $session): JsonResponse
    {
        $this->generateCaptcha($session);
        return new JsonResponse(['captcha' => $session->get('gregwar_captcha')]);
    }

    private function generateCaptcha(SessionInterface $session): void
    {
        $captcha = new CaptchaBuilder();
        $captcha->build();
        $session->set('gregwar_captcha', $captcha->getPhrase());
    }
}