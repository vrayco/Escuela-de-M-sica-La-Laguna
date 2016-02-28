<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PreinscripcionEnCurso
 *
 * @ORM\Table(name="preinscripcion_en_curso")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PreinscripcionEnCursoRepository")
 */
class PreinscripcionEnCurso
{

    const ESTADO_PREINSCRITO = "PREINSCRITO";

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
     * @ORM\Column(name="numeroSorteo", type="integer")
     */
    private $numeroSorteo;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Curso", inversedBy="curso")
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id")
     */
    private $curso;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Preinscripcion", inversedBy="preinscripciones")
     * @ORM\JoinColumn(name="preinscripcion_id", referencedColumnName="id")
     */
    private $preinscripcion;

    public function __construct()
    {
        $this->estado = self::ESTADO_PREINSCRITO;
        $this->numeroLista = -1;
        $this->numeroSorteo = -1;
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
     * Set numeroSorteo
     *
     * @param integer $numeroSorteo
     * @return PreinscripcionEnCurso
     */
    public function setNumeroSorteo($numeroSorteo)
    {
        $this->numeroSorteo = $numeroSorteo;

        return $this;
    }

    /**
     * Get numeroSorteo
     *
     * @return integer 
     */
    public function getNumeroSorteo()
    {
        return $this->numeroSorteo;
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
