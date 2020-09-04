<?php

namespace App\Controller;

use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use App\Entity\Event;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class EventController extends AbstractController
{
    /**
     * @Route("/created_event", name="created_event")
     */
    public function created(EventRepository $repository)
    {        
        $events = $repository->findAll();

        return $this->render('event/created.html.twig', [
            'events' => $events,
        ]);
    }

     /**
     * @Route("/joined_event", name="joined_event")
     */
    public function join()
    {
        return $this->render('event/joined.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    /**
     * @Route("/creation", name="creation")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
         $eventForm = $this->createForm(EventFormType::class);
         $eventForm->handleRequest($request);
 
         if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $event = $eventForm->getData();             
            $event->setAuthor($this->getUser());

            $entityManager->persist($event);
            $entityManager->flush();
             $this->addFlash('success', 'Event created.');
             return $this->redirectToRoute('created');
         }
 
         return $this->render('event/creation.html.twig', [
            "form_title" => "Create Event",
             'event_form' => $eventForm->createView(),
         ]);
     }

     /**
      * @Route("/event_edit/{id}", name="event_edit")
      *
      */
     public function editEvent(Request $request, Event $event)
     {
         $this->denyAccessUnlessGranted('EDIT', $event);
         $entityManager = $this->getDoctrine()->getManager();

         $eventForm = $this->createForm(EventFormType::class, $event);
         $eventForm->handleRequest($request);

         if($eventForm->isSubmitted() && $eventForm->isValid())
         {
             $entityManager->flush();
             return $this->redirectToRoute("created_event");

         }
         return $this->render("event/creation.html.twig", [
            "form_title" => "Edit Event",
            "event_form" => $eventForm->createView(),
        ]);

     }

     /**
      * @Route("/delete-event/{id}", name="event_delete")
      * @IsGranted("DELETE", subject="event")
      */

      public function deleteEvent(Event $event)
      {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();
    
        return $this->redirectToRoute("created_event");
      }
}