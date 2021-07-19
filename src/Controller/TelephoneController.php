<?php

namespace App\Controller;



use App\Entity\User;
use App\Entity\Constructeur;
use App\Entity\Telephone;
use App\Form\TelephoneType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TelephoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class TelephoneController extends AbstractController
{
    /**
     * @Route("/telephone", name="telephone")
     */
    public function index(TelephoneRepository $repo): Response
    {
        $telephones = $repo->findAll();
        return $this->render('telephone/index.html.twig', [
            'controller_name' => 'TelephoneController',
            "telephones" => $telephones
        ]);
    }


    /**
     *
     * @Route("/telephone/show/{id}",name="telephone_show")
     *
     */
    public function show(Telephone $telephone): Response
    {

        return $this->render('telephone/show.html.twig', [
            'controller_name' => 'TelephoneController',
            "telephone" => $telephone
        ]);
    }

    /**
     *
     * @Route("/telephone/new",name="telephone_create")
     * @Route("/telephone/edit/{id}",name="telephone_edit")
     *
     */
    public function create(Request $req, EntityManagerInterface $manager, UserInterface  $user,Telephone $telephone = null): Response
    {
        $modeCreation = false;
        if (!$telephone) {
            $telephone = new Telephone();
            $telephone->setCreatedDate(new \DateTime());
            $telephone->setUser($user) ;
            $modeCreation = true;
        }
        $formulaire = $this->createForm(TelephoneType::class, $telephone);
        $formulaire->handleRequest($req);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $imageEnvoyee = $formulaire->get('image')->getData();
            if ($imageEnvoyee) {
                try {
                    // On cree un nom unique pour cette image avec uniqueId() et on gere son extention avec gessExtension
                    $nouveauNomImage = uniqid() . "." . $imageEnvoyee->guessExtension();
                    $modeEdition = true;
                    //déplace l'image du dossier tmp dans le repertoire public/uploads/images/téléphone
                    $imageEnvoyee->move(
                    //chemin cible ( c est le chemin par défaut dans config/services.yaml )
                        $this->getParameter('images_telephones'),
                        //nom de la cible (nouvelle adresse)
                        $nouveauNomImage
                    );
                    //dans le cas d'une edition on laisse l'oportunitée de changer la photo
                    if (!$modeCreation || ($modeEdition && $imageEnvoyee)) {
                        //attribut la nouvelle url au telephone
                        $telephone->setImage($nouveauNomImage);
                    }
                } catch (FileException $e) {
                    throw $e;
                    return $this->redirectToRoute('telephone');
                }
            }

            $manager->persist($telephone);
            $manager->flush();

            // redirection vers le chemin show
            return $this->redirectToRoute('telephone_show', ["id" => $telephone->getId()]);
        }
        return $this->render('telephone/new.html.twig', [
            'formulaireTelephone' => $formulaire->createView(),
            "creation" => $modeCreation,
            "telephone" => $telephone

        ]);


    }

/**
 *
 * @Route("/telephone/delete/{id}", name="telephone_delete")
 *
 */
public function delete(Telephone $telephone): Response
{
    $em = $this->getDoctrine()->getManager();
    $em->remove($telephone);
    $em->flush();
    return $this->redirectToRoute("telephone");


}



}
