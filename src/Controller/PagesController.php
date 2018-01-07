<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PagesController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {

        return $this->render('pages/home.html.twig');
    }
}
