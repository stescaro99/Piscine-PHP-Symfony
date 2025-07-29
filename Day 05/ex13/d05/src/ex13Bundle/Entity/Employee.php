<?php

namespace ex13Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="employees_ex13")
 */
class Employee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100, nullable=false, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $active;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $employed_since;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $employed_until;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     * @\Symfony\Component\Validator\Constraints\Choice({"8", "6", "4"})
     */
    private $hours;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     * @\Symfony\Component\Validator\Constraints\Choice({
     *   "manager", "account_manager", "qa_manager", "dev_manager", "ceo", "coo",
     *   "backend_dev", "frontend_dev", "qa_tester"
     * })
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="Employee")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", nullable=true)
     */
    private $manager;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $salary;

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getEmployedSince()
    {
        return $this->employed_since;
    }

    public function setEmployedSince($employed_since)
    {
        $this->employed_since = $employed_since;
    }

    public function getEmployedUntil()
    {
        return $this->employed_until;
    }

    public function setEmployedUntil($employed_until)
    {
        $this->employed_until = $employed_until;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getManager()
    {
        return $this->manager;
    }
    public function setManager($manager)
    {
        $this->manager = $manager;
    }
}