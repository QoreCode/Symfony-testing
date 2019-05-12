<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends AbstractController
{
    public function homepage()
    {
        return $this->render('homepage.html.twig', [
            'title'=>'SOME'
        ]);
    }

    public function notFound(){
        return $this->render('error.html.twig');
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof NotFoundHttpException) {
            $response = new RedirectResponse('/not-found');
            $event->setResponse($response);
        }
    }
}