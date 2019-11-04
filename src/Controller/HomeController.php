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
    public function index(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contacts = $entityManager->getRepository(AddressBook::class)->findAll();
        $contactsData = $contacts ? $contacts : [];
        $flag = $request->query->get('flag');

        return $this->render('index.html.twig', [
            'contactsData' => $contactsData,
            'flag' => $flag
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

            return $this->redirectToRoute('home', ['flag' => 'Contact created successfully!!']);
        }

        return $this->render('new_contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Matches /edit exactly
     *
     * @Route("/edit", name="edit_contact")
     */
    public function editContactAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contactId = $request->query->get('contactId');
        $contact = $entityManager->getRepository(AddressBook::class)->find($contactId);
        $form = $this->createForm(AddressBookType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $entityManager->flush();

            return $this->redirectToRoute('home', ['flag' => 'Contact updated successfully!!']);
        }

        return $this->render('edit_contact.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }

    /**
     * Matches /show exactly
     *
     * @Route("/show", name="show_contact")
     */
    public function showContactAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contactId = $request->query->get('contactId');
        $contact = $entityManager->getRepository(AddressBook::class)->find($contactId);

        $form = $this->createForm(AddressBookType::class, $contact);

        $form->handleRequest($request);

        return $this->render('show_contact.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }

    /**
     * Matches /remove exactly
     *
     * @Route("/remove", name="remove_contact")
     */
    public function removeContactAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $contactId = $request->query->get('contactId');
        $contact = $entityManager->getRepository(AddressBook::class)->find($contactId);

        if ($request->isMethod("POST")) {
            $entityManager->remove($contact);
            $entityManager->flush();

            return $this->redirectToRoute('home', ['flag' => 'Contact removed successfully!!']);
        }

        return $this->render('remove_contact.html.twig', [
            'contact' => $contact
        ]);
    }
}
