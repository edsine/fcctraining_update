<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Mda;
use App\Entity\Training;
use App\Entity\TrainingParticipant;
use App\Entity\TrainingSession;
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

        $mda = $this->getDoctrine()
            ->getRepository(Mda::class)
            ->findOneBy([
            'mda_code' => $user->getMdacode()
        ]);


        // replace this line with your own code!
        return $this->render('user/mda.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'mda' => $mda

        ));
    }


    /**
     * @Route("/user/trainings", name="user_trainings")
     */
    public function user_trainings()
    {
        $user = $this->getUser();
        $page_title = "Training";

        $em = $this->getDoctrine()->getManager();


        $u_mda = $user->getMdaCode();

        // get all trainings
        $query2 = "SELECT training.* FROM training WHERE training.id NOT IN(
SELECT training_participant.training_id FROM training_participant WHERE training_participant.mda_code='$u_mda'
)";

        $statement2 = $em->getConnection()->prepare($query2);
        $statement2->execute();
        $training = $statement2->fetchAll();



        // get trainings applied for

        $mda_code = $user->getMdaCode();
        $query = "SELECT * FROM training_participant WHERE mda_code='$mda_code' GROUP BY mda_code,training_id";

        $statement = $em->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();


        // get information of trainings applied for
        $applied_trainings = array();

        foreach($result as $train)
        {

            $this_training = $this->getDoctrine()
                ->getRepository(Training::class)
                ->find($train['training_id']);

            array_push($applied_trainings, $this_training);
        }


        return $this->render('user/trainings.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'trainings' => $training,
            'trainings_applied' => $applied_trainings
        ));
    }



    /**
     * @Route("/user/training/{id}/apply", name="user_training_apply")
     */
    public function user_apply_training(Request $request, $id)
    {
        $user = $this->getUser();
        $page_title = "Training";

        $training = $this->getDoctrine()
            ->getRepository(Training::class)
            ->find($id);



        $training_session = $this->getDoctrine()
            ->getRepository(TrainingSession::class)
            ->findBy([
               'training_id' => $id,
                'status' => '1'
            ]);




        $form_session = $request->request->get('training_session');
        $form_participants = $request->request->get('participants');

        if(!empty($form_session))
        {
            $em = $this->getDoctrine()->getManager();

            //generate invoice for the MDA
            $invoice = new Invoice();

            $invoice->setPaymentStatus("0");
            $invoice->setMdaCode($user->getMdaCode());
            $invoice->setTrainingId($training->getId());
            $invoice->setPaymentAmount("");

            // calculate invoice charge based on number of participants
            $all_participants = count($form_participants);

            if($all_participants > $training->getIndividualsPerMda())
            {
                $main_partcipants = $training->getIndividualsPerMda();
                $extra_partcipants = $all_participants - $training->getIndividualsPerMda();
            }else{
                $main_partcipants = $all_participants;
                $extra_partcipants = 0;
            }

            $main_partcipants_charge = $main_partcipants * $training->getIndividualAmount();

            $extra_partcipants_charge = $extra_partcipants * $training->getExtraPersonnelAmount();

            $total_charge = $main_partcipants_charge + $extra_partcipants_charge;

            $invoice->setPaymentAmount($total_charge);

            $em->persist($invoice);
            $em->flush();


            foreach($form_participants as $part) {

                if(!empty($part)) {

                    // save training participants
                    $session = new TrainingParticipant();

                    $session->setTrainingId($training->getId());
                    $session->setMdaCode($user->getMdaCode());
                    $session->setParticipantName($part);
                    $session->setSessionId($form_session);
                    $session->setAttended("0");
                    $session->setInvoiceId($invoice->getId());


                    $em->persist($session);
                    $em->flush();

                }
            }


            // check if training session is full
            $training_id = $training->getId();

            $qu = $em->getConnection()->prepare("SELECT * FROM training_participant WHERE training_id='$training_id' AND session_id='$form_session'");
            $qu->execute();
            $stt = $qu->fetchAll();

            $t_session = $this->getDoctrine()
                ->getRepository(TrainingSession::class)
                ->find($form_session);

            if(count($stt) >= $t_session->getCapacity())
            {

                $t_session->setStatus('2');

                $em->persist($t_session);
                $em->flush();

            }


                return $this->redirectToRoute('user_view_training', array(
                    'id' => $training_id
                ));


        }


        return $this->render('user/apply_training.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'training' => $training,
            'training_sessions' => $training_session
        ));
    }




    /**
     * @Route("/user/training/{id}", name="user_view_training")
     */
    public function user_viewtraining(Request $request, $id)
    {


        $user = $this->getUser();
        $page_title = "Training";

        // get training information
        $training = $this->getDoctrine()
            ->getRepository(Training::class)
            ->find($id);


        $invoice = $this->getDoctrine()
            ->getRepository(Invoice::class)
            ->findby([
                'mda_code' => $user->getMdaCode(),
                'training_id' => $training->getId()
            ]);


        // get all mda participants for this training
        $training_participants = $this->getDoctrine()
            ->getRepository(TrainingParticipant::class)
            ->findby([
                'mda_code' => $user->getMdaCode(),
                'training_id' => $training->getId()
        ]);


        // start new mda participants array
        $participants = array();


        foreach($training_participants as $participant)
        {

            $training_session = $this->getDoctrine()
                ->getRepository(TrainingSession::class)
                ->find($participant->getSessionId());

            $invoice = $this->getDoctrine()
                ->getRepository(Invoice::class)
                ->find($participant->getInvoiceId());

            $row['name'] = $participant->getParticipantName();
            $row['session'] = $training_session->getName();
            $row['attended'] = $participant->getAttended();
            $row['payment_status'] = $invoice->getPaymentStatus();

            array_push($participants, $row);
        }


        print_r($invoice);

        return $this->render('user/view_training.html.twig', array(
            'user' => $user,
            'page_title' => $page_title,
            'training' => $training,
            'training_participants' => $participants,
            'invoices22' => $invoice
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
