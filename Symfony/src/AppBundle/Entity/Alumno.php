<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Alumno
 *
 * @ORM\Table(name="alumno")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AlumnoRepository")
 * @UniqueEntity({"dni"})
 */
class Alumno
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="expediente_letra", type="string", length=2)
     */
    private $expedienteLetra;
    /**
     * @var string
     *
     * @ORM\Column(name="expediente_numero", type="integer")
     */
    private $expedienteNumero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Assert\NotBlank(message="")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255)
     * @Assert\NotBlank(message="")
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=32, nullable=true, unique=true)
     */
    private $dni;

    /**
     * @var string
     *
     * @ORM\Column(name="anoIngreso", type="string", length=32, nullable=true)
     */
    private $anoIngreso;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=255)
     * @Assert\NotBlank(message="")
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255)
     * @Assert\NotBlank(message="")
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoPostal", type="string", length=16)
     * @Assert\NotBlank(message="")
     */
    private $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="telefonoFijo", type="string", length=32, nullable=true)
     */
    private $telefonoFijo;

    /**
     * @var string
     *
     * @ORM\Column(name="telefonoMovil", type="string", length=32, nullable=true)
     */
    private $telefonoMovil;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaNacimiento", type="date")
     * @Assert\NotBlank(message="")
     */
    private $fechaNacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Tutor", mappedBy="alumno", cascade={"persist","remove"})
     */
    private $tutor;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Matricula", mappedBy="alumno", cascade={"persist","remove"})
     */
    private $matriculas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createAt", type="datetime")
     */
    private $createAt;

    public function __construct()
    {
        $this->createAt = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get expediente
     *
     * @return string 
     */
    public function getExpediente()
    {
        return $this->expedienteLetra . '-' .sprintf('%04d', $this->expedienteNumero);
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Alumno
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return Alumno
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set dni
     *
     * @param string $dni
     * @return Alumno
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string 
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set anoIngreso
     *
     * @param string $anoIngreso
     * @return Alumno
     */
    public function setAnoIngreso($anoIngreso)
    {
        $this->anoIngreso = $anoIngreso;

        return $this;
    }

    /**
     * Get anoIngreso
     *
     * @return string 
     */
    public function getAnoIngreso()
    {
        return $this->anoIngreso;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     * @return Alumno
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return string 
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Alumno
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set codigoPostal
     *
     * @param string $codigoPostal
     * @return Alumno
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return string 
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set telefonoFijo
     *
     * @param string $telefonoFijo
     * @return Alumno
     */
    public function setTelefonoFijo($telefonoFijo)
    {
        $this->telefonoFijo = $telefonoFijo;

        return $this;
    }

    /**
     * Get telefonoFijo
     *
     * @return string 
     */
    public function getTelefonoFijo()
    {
        return $this->telefonoFijo;
    }

    /**
     * Set telefonoMovil
     *
     * @param string $telefonoMovil
     * @return Alumno
     */
    public function setTelefonoMovil($telefonoMovil)
    {
        $this->telefonoMovil = $telefonoMovil;

        return $this;
    }

    /**
     * Get telefonoMovil
     *
     * @return string 
     */
    public function getTelefonoMovil()
    {
        return $this->telefonoMovil;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Alumno
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Alumno
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime 
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return Alumno
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return Alumno
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set tutor
     *
     * @param \AppBundle\Entity\Tutor $tutor
     * @return Alumno
     */
    public function setTutor(\AppBundle\Entity\Tutor $tutor = null)
    {
        $this->tutor = $tutor;

        return $this;
    }

    /**
     * Get tutor
     *
     * @return \AppBundle\Entity\Tutor 
     */
    public function getTutor()
    {
        return $this->tutor;
    }

    /**
     * Add matriculas
     *
     * @param \AppBundle\Entity\Matricula $matriculas
     * @return Alumno
     */
    public function addMatricula(\AppBundle\Entity\Matricula $matriculas)
    {
        $this->matriculas[] = $matriculas;

        return $this;
    }

    /**
     * Remove matriculas
     *
     * @param \AppBundle\Entity\Matricula $matriculas
     */
    public function removeMatricula(\AppBundle\Entity\Matricula $matriculas)
    {
        $this->matriculas->removeElement($matriculas);
    }

    /**
     * Get matriculas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMatriculas()
    {
        return $this->matriculas;
    }

    /**
     * Set expedienteLetra
     *
     * @param string $expedienteLetra
     * @return Alumno
     */
    public function setExpedienteLetra($expedienteLetra)
    {
        $this->expedienteLetra = $expedienteLetra;

        return $this;
    }

    /**
     * Get expedienteLetra
     *
     * @return string 
     */
    public function getExpedienteLetra()
    {
        return $this->expedienteLetra;
    }

    /**
     * Set expedienteNumero
     *
     * @param integer $expedienteNumero
     * @return Alumno
     */
    public function setExpedienteNumero($expedienteNumero)
    {
        $this->expedienteNumero = $expedienteNumero;

        return $this;
    }

    /**
     * Get expedienteNumero
     *
     * @return integer 
     */
    public function getExpedienteNumero()
    {
        return $this->expedienteNumero;
    }
}
