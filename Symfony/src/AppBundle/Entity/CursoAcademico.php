<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CursoAcademico
 *
 * @ORM\Table(name="curso_academico")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CursoAcademicoRepository")
 */
class CursoAcademico
{

    const CURSO_ACADEMICO_SESSION_VAR = "CURSO_ACADEMICO_ACTUAL";

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="datetime")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin", type="datetime")
     */
    private $fechaFin;

    /**
     * @ORM\Column(name="prefijo_expediente", type="string", length=2, unique=true)
     */
    private $prefijoExpediente;

    /**
     * @ORM\Column(name="en_curso", type="boolean")
     */
    private $enCurso;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Curso", mappedBy="cursoAcademico", cascade={"persist","remove"})
     */
    private $cursos;

    public function __construct()
    {
        $this->cursos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enCurso = false;
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
     * Set nombre
     *
     * @param string $nombre
     * @return CursoAcademico
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return CursoAcademico
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return CursoAcademico
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Add cursos
     *
     * @param \AppBundle\Entity\Curso $cursos
     * @return CursoAcademico
     */
    public function addCurso(\AppBundle\Entity\Curso $cursos)
    {
        $this->cursos[] = $cursos;

        return $this;
    }

    /**
     * Remove cursos
     *
     * @param \AppBundle\Entity\Curso $cursos
     */
    public function removeCurso(\AppBundle\Entity\Curso $cursos)
    {
        $this->cursos->removeElement($cursos);
    }

    /**
     * Get cursos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCursos()
    {
        return $this->cursos;
    }

    /**
     * Set prefijoExpediente
     *
     * @param string $prefijoExpediente
     * @return CursoAcademico
     */
    public function setPrefijoExpediente($prefijoExpediente)
    {
        $this->prefijoExpediente = $prefijoExpediente;

        return $this;
    }

    /**
     * Get prefijoExpediente
     *
     * @return string 
     */
    public function getPrefijoExpediente()
    {
        return $this->prefijoExpediente;
    }

    /**
     * Set enCurso
     *
     * @param boolean $enCurso
     * @return CursoAcademico
     */
    public function setEnCurso($enCurso)
    {
        $this->enCurso = $enCurso;

        return $this;
    }

    /**
     * Get enCurso
     *
     * @return boolean 
     */
    public function getEnCurso()
    {
        return $this->enCurso;
    }
}
