<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register_form', methods: ['GET'])]
    public function registerForm(
        // ...
    ): Response
    {
        return $this->render('registration/register.html.twig');
    }


    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Валидация данных
        if (empty($email) || empty($password)) {
            // Обработайте ошибку валидации
            return $this->render('registration/register.html.twig', [
                'error' => 'Пожалуйста, введите email и пароль.',
            ]);
        }

        $user = new \App\Entity\User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
}
