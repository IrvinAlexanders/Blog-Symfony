<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\ContactType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link = $this->getDoctrine()->getManager();

            $message->setCreated(new DateTime('now'));

            $link->persist($message);
            $link->flush();
            $this->addFlash('correcto', Messages::REGISTRO_EXITOSO);
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'formulario' => $form->createView()
        ]);
    }
}
