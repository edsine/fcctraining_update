<?php

namespace App\Controller;

use App\Entity\Training;
use App\Entity\TrainingParticipant;
use App\Entity\User;
use App\Entity\Mda;
use App\Entity\MdaParticipant;


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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminController extends Controller
{

    /**
     * AdminController constructor.
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function __construct()
    {

    }

    /**
     * @Route("/admin", name="admin_dashboard")
     * @return Response
     */
    public function index()
    {
        $user = $this->getUser();
        $page_title = "Dashboard";

        // replace this line with your own code!
        return $this->render('admin/dashboard.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }


    /**
     * @Route("/admin/mda", name="admin_mda")
     */
    public function mdas()
    {

        $em = $this->getDoctrine()->getManager();

        $query = "SELECT * FROM mda";


        // fetch all mdas
        $statement = $em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();

        $user = $this->getUser();
        $page_title = "MDAs";


        return $this->render('admin/mdas.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'mdas' => $result
        ));
    }


    /**
     * @Route("/admin/mda/add", name="add_mda")
     */
    public function add_mda(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $page_title = "MDAs";

        $mda = new Mda();

        $form = $this->createFormBuilder($mda)
            ->add('Username', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3'
                )
            ))
            ->add('Email', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3'
                )
            ))
            ->add('Address', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'rows' => '4'
                )
            ))
            ->add('Phone', IntegerType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'onkeypress' => 'return isNumber(event)'
                )
            ))
            ->add('Mda_Code', IntegerType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'onkeypress' => 'return isNumber(event)'
                )
            ))
            ->add('Submit', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-primary btn-block mb-3'
                )
            ))
            ->getForm();


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $mda = $form->getData();

            $mda->setPassword($encoder->encodePassword($mda, $mda->getMdaCode()));

            $mda->setRoles('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($mda);
            $em->flush();

            $this->addFlash('success', 'New Mda Added');

            return $this->redirectToRoute('admin_mda');

        }


        return $this->render('admin/add_mda.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'form' => $form->createView()
        ));
    }



    /**
     * @Route("/admin/participants", name="admin_participants")
     */
    public function participants()
    {
        $user = $this->getUser();
        $page_title = "Participants";

        $participants = $this->getDoctrine()
            ->getRepository(mdaParticipant::class)
            ->findAll();

        return $this->render('admin/participants.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'participants' => $participants
        ));
    }

    /**
     * @Route("/admin/participant/{id}", name="admin_view_participants")
     */
    public function view_participants($id)
    {
        $user = $this->getUser();
        $page_title = "Participants";

        // get participant info
        $participant = $this->getDoctrine()
            ->getRepository(mdaParticipant::class)
            ->find($id);

        // get participant trainings applied
        $applied_training = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findBy([
                'participant_id' => $id
            ]);

        // start participant trainings array
        $participants_trainings = array();


        foreach($applied_training as $train)
        {
            $training = $this->getDoctrine()
                ->getRepository(Training::class)
                ->find($train->getTrainingId());


            array_push($participants_trainings, $training);
        }



        // get mda info
        $mda = $this->getDoctrine()
            ->getRepository(mda::class)
            ->findOneBy(['mda_code' => $participant->getMdaCode()]);


        return $this->render('admin/view_participant.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'participant' => $participant,
            'mda' => $mda,
            'trainings' => $participants_trainings
        ));
    }

    /**
     * @Route("/admin/participants/{mda_code}", name="admin_mda_participants")
     */
    public function mda_participants(Request $request, $mda_code)
    {
        $user = $this->getUser();
        $page_title = "Participants";


        return $this->render('admin/participants.html.twig', array(
            'user' => $user,
            'page_title' => $page_title
        ));
    }

    /**
     * @Route("/admin/training", name="admin_training")
     */
    public function training()
    {
        $user = $this->getUser();
        $page_title = "Training";


        $training  = $this->getDoctrine()
            ->getRepository(Training::class)
            ->findAll();

        return $this->render('admin/training.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'trainings' => $training
        ));
    }


    /**
     * @Route("/admin/training/add", name="admin_add_training")
     */
    public function add_training(Request $request)
    {
        $user = $this->getUser();
        $page_title = "Training";

        $training = new training();

       $form = $this->createFormBuilder($training)
           ->add('Title', TextType::class, array(
               'attr' => array(
                   'class' => 'form-control mb-3'
               )
           ))
           ->add('Venue', TextType::class, array(
               'attr' => array(
                   'class' => 'form-control mb-3'
               )
           ))
           ->add('Date', DateType::class, array(
               //'format' => 'yyyy-MM-dd',
               'widget' => 'single_text',
               'html5' => false,
               'attr' => array(
                   'class' => 'form-control mb-3 datepicker',
                   'readonly' => ''
               )
           ))
           ->add('Time', TimeType::class, array(
               'hours' => array(
                 '01', '02','03','04','05','06','07','08','09','10','11','12'
               ),
               //'with_seconds' => true,
               'attr' => array(
                   'class' => 'mb-3'
               )
           ))
           ->add('Registration_fee', MoneyType::class, array(
               'currency' => 'NGN',
               'grouping' => true,
               'attr' => array(
                   'class' => 'form-control mb-3',
                   'onkeypress' => 'return isNumber(event)'
               )
           ))
           ->add('Individual_Amount', MoneyType::class, array(
               'currency' => 'NGN',
               'grouping' => true,
               'attr' => array(
                   'class' => 'form-control mb-3',
                   'onkeypress' => 'return isNumber(event)'
               )
           ))
           ->add('Extra_personnel_amount', MoneyType::class, array(
               'currency' => 'NGN',
               'grouping' => true,
               'attr' => array(
                   'class' => 'form-control mb-3',
                   'onkeypress' => 'return isNumber(event)'
               )
           ))
           ->add('Individuals_per_mda', NumberType::class, array(
               'attr' => array(
                   'class' => 'form-control mb-3',
                   'maxlength' => '1',
                   'onkeypress' => 'return isNumber(event)'
               )
           ))
           ->add('Submit', SubmitType::class, array(
               'attr' => array(
                   'class' => 'btn btn-primary btn-block'
               )
           ))
           ->getForm();

       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid())
       {

           $form_data = $form->getData();

           $em = $this->getDoctrine()->getManager();
           $em->persist($form_data);
           $em->flush();

           $this->addFlash('success', 'New Mda Added');

           return $this->redirectToRoute('admin_training');


       }



        return $this->render('admin/add_training.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/admin/training/edit/{id}", name="admin_edit_training")
     */
    public function edit_training(Request $request, $id)
    {
        $user = $this->getUser();
        $page_title = "Training";

        $training = $this->getDoctrine()
            ->getRepository(Training::class)
            ->find($id);

        $form = $this->createFormBuilder($training)
            ->add('Title', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3'

                )
            ))
            ->add('Venue', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3'
                )
            ))
            ->add('Date', DateType::class, array(
                //'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                    'class' => 'form-control mb-3 datepicker',
                    'readonly' => ''
                )
            ))
            ->add('Time', TimeType::class, array(
                'hours' => array(
                    '01', '02','03','04','05','06','07','08','09','10','11','12'
                ),
                //'with_seconds' => true,
                'attr' => array(
                    'class' => 'mb-3'
                )
            ))
            ->add('Registration_fee', MoneyType::class, array(
                'currency' => 'NGN',
                'grouping' => true,
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'onkeypress' => 'return isNumber(event)'
                )
            ))
            ->add('Individual_Amount', MoneyType::class, array(
                'currency' => 'NGN',
                'grouping' => true,
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'onkeypress' => 'return isNumber(event)'
                )
            ))
            ->add('Extra_personnel_amount', MoneyType::class, array(
                'currency' => 'NGN',
                'grouping' => true,
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'onkeypress' => 'return isNumber(event)'
                )
            ))
            ->add('Individuals_per_mda', NumberType::class, array(
                'attr' => array(
                    'class' => 'form-control mb-3',
                    'maxlength' => '1',
                    'onkeypress' => 'return isNumber(event)'
                )
            ))

            ->add('Letter_content', TextareaType::class, array(
                'attr' => array(
                    'class' => 'trumbowyg-demo mb-3 form-control',
                    'rows' => '15'
                )
            ))

            ->add('Update Training', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-success btn-block'
                )
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $form_data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($form_data);
            $em->flush();

            $this->addFlash('success', 'Mda Updated');

            return $this->redirectToRoute('admin_training');


        }



        return $this->render('admin/update_training.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/admin/training/delete/{id}", name="admin_delete_training")
     *
     */
    public function delete_training($id)
    {
        $training = $this->getDoctrine()
            ->getRepository(Training::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($training);
        $em->flush();

        $this->addFlash('success', 'Mda Deleted');

        return $this->redirectToRoute('admin_training');

    }


    /**
     * @Route("/admin/training/{id}/participants", name="admin_training_participants")
     *
     */
    public function training_participants($id)
    {
        $user = $this->getUser();
        $page_title = "Training Participants";

        // get training information
        $training = $this->getDoctrine()
            ->getRepository(Training::class)
            ->find($id);


        // get training participants information
        $training_participant = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findAll();

        $em = $this->getDoctrine()->getManager();


        // select and sum all card payments
        $query = "SELECT SUM(amount_paid) AS total_card_amount FROM training_participant WHERE payment_method='card' AND training_id='$id'";

        $statement = $em->getConnection()->prepare($query);
        $statement->execute();
        $card_amount = $statement->fetchAll();


        // select and sum all cash payments
        $query2 = "SELECT SUM(amount_paid) AS total_cash_amount FROM training_participant WHERE payment_method='cash' AND training_id='$id'";

        $statement2 = $em->getConnection()->prepare($query2);
        $statement2->execute();
        $cash_amount = $statement2->fetchAll();


        // select and sum all undertaken
        $undertaken_count = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findBy([
               'payment_method' => 'undertaken',
                'training_id' => $id
            ]);


        // start new training participant array
        $t_participant = array();

        // extract and arrange the training participant array
        foreach($training_participant as $tp) {

            $part = $this->getDoctrine()
                ->getRepository(MdaParticipant::class)
                ->find($tp->getParticipantId());

            $mda = $this->getDoctrine()
                ->getRepository(Mda::class)
                ->findOneBy(['mda_code' => $tp->getMdaCode()]);

            $m['participantid'] = $tp->getParticipantId();
            $m['participant'] = $part->getUsername();
            $m['mdacode'] = $tp->getMdaCode();
            $m['mda'] = $mda->getName();
            $m['amountpaid'] = $tp->getAmountPaid();
            $m['paymentmethod'] = $tp->getPaymentMethod();
            $m['paymentstatus'] = $tp->getPaymentStatus();
            $m['paymentdate'] = $tp->getPaymentDATE();


            array_push($t_participant, $m);
        }


        // render to view
        return $this->render('admin/training_participants.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'training_participants' => $t_participant,
            'training' => $training,
            'count_participant' => count($t_participant),
            'total_card' => $card_amount[0]['total_card_amount'],
            'total_cash' => $cash_amount[0]['total_cash_amount'],
            'total_undertaken' => count($undertaken_count)
        ));

    }

        /**
     * @Route("/admin/financial", name="admin_financials")
     */
    public function financials()
    {
        $user = $this->getUser();
        $page_title = "Financials";

        $em = $this->getDoctrine()->getManager();

        // select and sum all card payments
        $query = "SELECT SUM(amount_paid) AS total_card_amount FROM training_participant WHERE payment_method='card' ";

        $statement = $em->getConnection()->prepare($query);
        $statement->execute();
        $card_amount = $statement->fetchAll();


        // select and sum all cash payments
        $query2 = "SELECT SUM(amount_paid) AS total_cash_amount FROM training_participant WHERE payment_method='cash' ";

        $statement2 = $em->getConnection()->prepare($query2);
        $statement2->execute();
        $cash_amount = $statement2->fetchAll();


        // select and sum all undertaken
        $undertaken_count = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findBy([
                'payment_method' => 'undertaken'
            ]);


        // get training participants information
        $training_participant = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findBy([
                'payment_method' => 'cash',
                'payment_method' => 'card'
            ]);

        // get training participants information
        $outstanding_training_participant = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findBy([
                'payment_method' => 'undertaken'
            ]);


        // start new training participant array
        $t_participant = array();

        // start new outstanding training participant array
        $outstanding_participants = array();


        // extract and arrange the training participant array
        foreach($training_participant as $tp) {

            $part = $this->getDoctrine()
                ->getRepository(MdaParticipant::class)
                ->find($tp->getParticipantId());

            // get training information
            $training = $this->getDoctrine()
                ->getRepository(Training::class)
                ->find($tp->getTrainingId());

            // get mda information
            $mda = $this->getDoctrine()
                ->getRepository(Mda::class)
                ->findOneBy(['mda_code' => $tp->getMdaCode()]);

            // assign variables
            $m['participantid'] = $tp->getParticipantId();
            $m['participant'] = $part->getUsername();
            $m['mdacode'] = $tp->getMdaCode();
            $m['mda'] = $mda->getName();
            $m['training_id'] = $training->getId();
            $m['training_title'] = $training->getTitle();
            $m['amountpaid'] = $tp->getAmountPaid();
            $m['paymentmethod'] = $tp->getPaymentMethod();
            $m['paymentstatus'] = $tp->getPaymentStatus();
            $m['paymentdate'] = $tp->getPaymentDATE();


            array_push($t_participant, $m);
        }



        // extract and arrange the outstanding training participant array
        foreach($outstanding_training_participant as $tp) {

            $part = $this->getDoctrine()
                ->getRepository(MdaParticipant::class)
                ->find($tp->getParticipantId());

            // get training information
            $training = $this->getDoctrine()
                ->getRepository(Training::class)
                ->find($tp->getTrainingId());

            // get mda information
            $mda = $this->getDoctrine()
                ->getRepository(Mda::class)
                ->findOneBy(['mda_code' => $tp->getMdaCode()]);

            // assign variables
            $m['participantid'] = $tp->getParticipantId();
            $m['participant'] = $part->getUsername();
            $m['mdacode'] = $tp->getMdaCode();
            $m['mda'] = $mda->getName();
            $m['training_id'] = $training->getId();
            $m['training_title'] = $training->getTitle();
            $m['outstandingamount'] = $training->getIndividualAmount();


            array_push($outstanding_participants, $m);
        }


        return $this->render('admin/financials.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'training_participants' => $t_participant,
            'outstanding_participants' => $outstanding_participants,
            'total_card' => $card_amount[0]['total_card_amount'],
            'total_cash' => $cash_amount[0]['total_cash_amount'],
            'total_undertaken' => count($undertaken_count)
        ));
    }
}
