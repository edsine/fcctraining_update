<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainingParticipantRepository")
 */
class TrainingParticipant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="integer")
     */
    private $training_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $participant_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $mda_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount_paid;

    /**
     * @ORM\Column(type="string")
     */
    private $payment_method;

    /**
     * @ORM\Column(type="integer")
     */
    private $payment_status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $payment_date;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTrainingId()
    {
        return $this->training_id;
    }

    /**
     * @param mixed $training_id
     */
    public function setTrainingId($training_id): void
    {
        $this->training_id = $training_id;
    }

    /**
     * @return mixed
     */
    public function getParticipantId()
    {
        return $this->participant_id;
    }

    /**
     * @param mixed $participant_id
     */
    public function setParticipantId($participant_id): void
    {
        $this->participant_id = $participant_id;
    }

    /**
     * @return mixed
     */
    public function getMdaCode()
    {
        return $this->mda_code;
    }

    /**
     * @param mixed $mda_code
     */
    public function setMdaCode($mda_code): void
    {
        $this->mda_code = $mda_code;
    }

    /**
     * @return mixed
     */
    public function getAmountPaid()
    {
        return $this->amount_paid;
    }

    /**
     * @param mixed $amount_paid
     */
    public function setAmountPaid($amount_paid): void
    {
        $this->amount_paid = $amount_paid;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @param mixed $payment_method
     */
    public function setPaymentMethod($payment_method): void
    {
        $this->payment_method = $payment_method;
    }

    /**
     * @return mixed
     */
    public function getPaymentStatus()
    {
        return $this->payment_status;
    }

    /**
     * @param mixed $payment_status
     */
    public function setPaymentStatus($payment_status): void
    {
        $this->payment_status = $payment_status;
    }

    /**
     * @return mixed
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * @param mixed $payment_date
     */
    public function setPaymentDate($payment_date): void
    {
        $this->payment_date = $payment_date;
    }



}
