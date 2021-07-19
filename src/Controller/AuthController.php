<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     *
     */
    public function register(Request $req, EntityManagerInterface $manager,UserPasswordHasherInterface $hasher ): Response
    {
        $user = new User();
        $formulaire = $this->createForm(RegisterType::class, $user);
        $formulaire->handleRequest($req);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $hashedPassword = $hasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->render('auth/register.html.twig', [
            'formulaireRegister' => $formulaire->createView(),
            'controller_name' => 'AuthController',

            ]);
        //return $this->redirectToRoute('telephone');

    }

    /**
     *
     * @Route ("/login", name="login")
     */
    public function login()
    {
    return $this->render('auth/login.html.twig');
    }

    /**
     *
     * @Route ("/logout", name="logout")
     *
     */
    public function logout()
    {

    }
}
