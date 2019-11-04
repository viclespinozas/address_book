<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\AddressBook;
use App\Form\AddressBookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contacts = $entityManager->getRepository(AddressBook::class)->findAll();
        $contactsData = $contacts ? $contacts : [];

        return $this->render('index.html.twig', [
            'contactsData' => $contactsData,
        ]);
    }

    /**
     * Matches /new exactly
     *
     * @Route("/new", name="new_contact")
     */
    public function newContactAction(Request $request)
    {
        $contact = new AddressBook();
        $form = $this->createForm(AddressBookType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('new_contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
