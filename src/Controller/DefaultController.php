<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function homepage()
    {
        return new Response('ONG');
    }
}