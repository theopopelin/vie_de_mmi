<?php

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class Lobby extends AbstractController {

    /**
     * @Route("/", name="app_lobby")
     */

    public function lobby(){


        return $this->render('lobby.html.twig');
    }
}