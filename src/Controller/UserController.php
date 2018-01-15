<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{


    /**
     * @Route("/user", name="user_dashboard")
     */
    public function index()
    {
        $user = $this->getUser();
        $page_title = "Dashboard";

        // replace this line with your own code!
        return $this->render('user/dashboard.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }


    /**
     * @Route("/user/mda", name="user_mda")
     */
    public function user_mda()
    {
        $user = $this->getUser();
        $page_title = "MDA";

        // replace this line with your own code!
        return $this->render('user/mda.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }


    /**
     * @Route("/user/trainings", name="user_trainings")
     */
    public function user_trainings()
    {
        $user = $this->getUser();
        $page_title = "Training";

        // replace this line with your own code!
        return $this->render('user/trainings.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }


    /**
     * @Route("/user/invoice", name="user_invoice")
     */
    public function user_invoice()
    {
        $user = $this->getUser();
        $page_title = "Invoice";

        // replace this line with your own code!
        return $this->render('user/invoice.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }


    /**
     * @Route("/user/account", name="user_account")
     */
    public function user_manage_account()
    {
        $user = $this->getUser();
        $page_title = "Account";

        // replace this line with your own code!
        return $this->render('user/account.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }
}
