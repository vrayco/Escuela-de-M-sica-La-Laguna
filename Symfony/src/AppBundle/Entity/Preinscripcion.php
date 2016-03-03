<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppBundleAssert;

/**
 * Preinscripcion
 *
 * @ORM\Table(name="preinscripcion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PreinscripcionRepository")
 * @UniqueEntity({"nombre","apellidos","fechaNacimiento","prefijo"},
 *    errorPath="nombre",
 *    message="Existe una preinscripcion con estos datos."
 * )
 *
 * @UniqueEntity({"nombre","apellidos","fechaNacimiento","prefijo"},
 *    errorPath="apellidos",
 *    message="Existe una preinscripcion con estos datos."
 * )
 *
 * @UniqueEntity({"nombre","apellidos","fechaNacimiento","prefijo"},
 *    errorPath="fechaNacimiento",
 *    message="Existe una preinscripcion con estos datos."
 * )
 *
 * @UniqueEntity({"dni","prefijo"},
 *    errorPath="dni",
 *    message="Existe una preinscripcion con estos datos."
 * )
 *
 * @AppBundleAssert\Preinscripcion()
 */

class Preinscripcion
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
     * @ORM\Column(name="prefijo", type="string", length=2)
     */
    private $prefijo;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=32, nullable=true)
     */
    private $dni;

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
     * @var \Date
     *
     * @ORM\Column(name="fechaNacimiento", type="date")
     * @Assert\NotBlank(message="")
     */
    private $fechaNacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="telefonoMovil", type="string", length=32, nullable=true)
     * @Assert\NotBlank(message="")
     */
    private $telefonoMovil;

    /**
     * @var bool
     *
     * @ORM\Column(name="prioridad", type="boolean")
     */
    private $prioridad;

    /**
     * @var bool
     *
     * @ORM\Column(name="empadronado", type="boolean")
     */
    private $empadronado;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PreinscripcionEnCurso", mappedBy="preinscripcion", cascade={"persist","remove"})
     * @Assert\Valid
     */
    private $preinscripcionEnCursos;

    public function getIdentificador()
    {
        return "PRE-" . $this->prefijo.$this->id;
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
     * Set dni
     *
     * @param string $dni
     * @return Preinscripcion
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
     * Set nombre
     *
     * @param string $nombre
     * @return Preinscripcion
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
     * @return Preinscripcion
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
     * Set telefonoMovil
     *
     * @param string $telefonoMovil
     * @return Preinscripcion
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
     * Set prioridad
     *
     * @param boolean $prioridad
     * @return Preinscripcion
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return boolean 
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set empadronado
     *
     * @param boolean $empadronado
     * @return Preinscripcion
     */
    public function setEmpadronado($empadronado)
    {
        $this->empadronado = $empadronado;

        return $this;
    }

    /**
     * Get empadronado
     *
     * @return boolean 
     */
    public function getEmpadronado()
    {
        return $this->empadronado;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->preinscripcionEnCursos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add preinscripcionEnCursos
     *
     * @param \AppBundle\Entity\PreinscripcionEnCurso $preinscripcionEnCursos
     * @return Preinscripcion
     */
    public function addPreinscripcionEnCurso(\AppBundle\Entity\PreinscripcionEnCurso $preinscripcionEnCursos)
    {
        $this->preinscripcionEnCursos[] = $preinscripcionEnCursos;

        return $this;
    }

    /**
     * Remove preinscripcionEnCursos
     *
     * @param \AppBundle\Entity\PreinscripcionEnCurso $preinscripcionEnCursos
     */
    public function removePreinscripcionEnCurso(\AppBundle\Entity\PreinscripcionEnCurso $preinscripcionEnCursos)
    {
        $this->preinscripcionEnCursos->removeElement($preinscripcionEnCursos);
    }

    /**
     * Get preinscripcionEnCursos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPreinscripcionEnCursos()
    {
        return $this->preinscripcionEnCursos;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Preinscripcion
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
     * Set prefijo
     *
     * @param string $prefijo
     * @return Preinscripcion
     */
    public function setPrefijo($prefijo)
    {
        $this->prefijo = $prefijo;

        return $this;
    }

    /**
     * Get prefijo
     *
     * @return string 
     */
    public function getPrefijo()
    {
        return $this->prefijo;
    }
}
