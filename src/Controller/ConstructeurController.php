<?php

namespace App\Controller;

use App\Entity\Constructeur;
use App\Form\ConstructeurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConstructeurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConstructeurController extends AbstractController
{
    /**
     * @Route("/constructeur", name="constructeur")
     */
    public function index(ConstructeurRepository $repo): Response
    {
        $constructeurs = $repo->findAll();
        return $this->render('constructeur/index.html.twig', [
            'controller_name' => 'ConstructeurController',
            "constructeurs" => $constructeurs
        ]);
    }

    /**
     *
     * @Route ("/constructeur/show/{id}" ,name="constructeur_show")
     *
     */
    public function show(Constructeur $constructeur): Response
    {
        return $this->render('constructeur/show.html.twig', [
            'controller_name' => 'ConstructeurController',
            'constructeur' => $constructeur]);
    }

    /**
     *
     * @Route("/constructeur/new",name="constructeur_create")
     * @Route("/constructeur/edit/{id}" ,name="constructeur_edit")
     *
     */
    public function create(Request $req, EntityManagerInterface $manager, Constructeur $constructeur = null): Response
    {
        $modeCreation = false;
        if (!$constructeur) {
            $constructeur = new Constructeur();
            $modeCreation = true;
        }
        $formulaire = $this->createForm(ConstructeurType::class,$constructeur);
        $formulaire->handleRequest($req);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $imageEnvoyee = $formulaire->get('imageLogo')->getData();
            if ($imageEnvoyee) {
                try {
                    $nouveauNomImage = uniqid() . "." . $imageEnvoyee->guessExtension();
                    $modeEdition = true;
                    $imageEnvoyee->move(
                        $this->getParameter('images_constructeurs'),
                        $nouveauNomImage
                    );
                    if (!$modeCreation || ($modeEdition && $imageEnvoyee)) {
                        //attribut la nouvelle url au telephone
                        $constructeur->setImageLogo($nouveauNomImage);
                    }



                } catch (FileException $e) {
                    throw $e;
                    return $this->redirectToRoute('constructeur');
                }
            }
            $manager->persist($constructeur);
            $manager->flush();
            return $this->redirectToRoute('constructeur_show', ["id" => $constructeur->getId()]);
        }
        return $this->render('constructeur/new.html.twig', [
            'formulaireConstructeur' => $formulaire->createView(),
            'creation' => $modeCreation,
            'constructeur' => $constructeur
        ]);
    }

    /**
     *
     * @Route("/constructeur/delete/{id}" ,name="constructeur_delete")
     *
     */
    public function delete(Constructeur $constructeur): response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($constructeur);
        $em->flush();
        return $this->redirectToRoute('constructeur');
    }
}
