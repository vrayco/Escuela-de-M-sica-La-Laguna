<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PreinscripcionEnCurso
 *
 * @ORM\Table(name="preinscripcion_en_curso")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PreinscripcionEnCursoRepository")
 * @UniqueEntity({"curso","preinscripcion"},
 *    errorPath="curso",
 *    message="Disciplina repetida en la preinscripción.")
 */
class PreinscripcionEnCurso
{

    const ESTADO_PREINSCRITO    = "PREINSCRITO";
    const ESTADO_PLAZA          = "PLAZA";
    const ESTADO_ACEPTADA       = "ACEPTADA";
    const ESTADO_RECHAZADA      = "RECHAZADA";
    const ESTADO_RESERVA        = "RESERVA";

    const SIN_PLAZA = -1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroLista", type="integer", nullable=true)
     */
    private $numeroLista;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=32, nullable=true)
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Curso", inversedBy="preinscripciones")
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id", nullable=false)
     */
    private $curso;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Preinscripcion", inversedBy="preinscripcionEnCursos")
     * @ORM\JoinColumn(name="preinscripcion_id", referencedColumnName="id", nullable=false)
     */
    private $preinscripcion;

    public function __construct()
    {
        $this->estado = self::ESTADO_PREINSCRITO;
        $this->numeroLista = self::SIN_PLAZA;
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
     * Set numeroLista
     *
     * @param integer $numeroLista
     * @return PreinscripcionEnCurso
     */
    public function setNumeroLista($numeroLista)
    {
        $this->numeroLista = $numeroLista;

        return $this;
    }

    /**
     * Get numeroLista
     *
     * @return integer 
     */
    public function getNumeroLista()
    {
        return $this->numeroLista;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return PreinscripcionEnCurso
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set curso
     *
     * @param \AppBundle\Entity\Curso $curso
     * @return PreinscripcionEnCurso
     */
    public function setCurso(\AppBundle\Entity\Curso $curso = null)
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * Get curso
     *
     * @return \AppBundle\Entity\Curso 
     */
    public function getCurso()
    {
        return $this->curso;
    }

    /**
     * Set preinscripcion
     *
     * @param \AppBundle\Entity\Preinscripcion $preinscripcion
     * @return PreinscripcionEnCurso
     */
    public function setPreinscripcion(\AppBundle\Entity\Preinscripcion $preinscripcion = null)
    {
        $this->preinscripcion = $preinscripcion;

        return $this;
    }

    /**
     * Get preinscripcion
     *
     * @return \AppBundle\Entity\Preinscripcion 
     */
    public function getPreinscripcion()
    {
        return $this->preinscripcion;
    }
}
