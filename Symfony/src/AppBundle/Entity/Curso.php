<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppBundleAssert;

/**
 * Curso
 *
 * @ORM\Table(name="curso")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CursoRepository")
 * @UniqueEntity({"disciplina","cursoAcademico"},
 *     message="Ya existe un curso con esta disciplina para el curso en curso."
 * )
 * @AppBundleAssert\CursoPlazas()
 */
class Curso
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
     * @var int
     *
     * @ORM\Column(name="numeroPlazas", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     */
    private $numeroPlazas;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroPlazasPrioritarias", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     */
    private $numeroPlazasPrioritarias;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CursoAcademico", inversedBy="cursos", cascade={"persist"})
     * @ORM\JoinColumn(name="curso_academico_id", referencedColumnName="id", nullable=false)
     */
    private $cursoAcademico;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Disciplina", inversedBy="cursos")
     * @ORM\JoinColumn(name="disciplina_id", referencedColumnName="id", nullable=false)
     */
    private $disciplina;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Matricula", mappedBy="curso", cascade={"persist","remove"})
     */
    private $matriculas;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PreinscripcionEnCurso", mappedBy="curso", cascade={"persist","remove"})
     */
    private $preinscripciones;

    public function __toString()
    {
        return $this->disciplina->getNombre() . ' (' .$this->disciplina->getDisciplinaGrupo()->getNombre(). ')';
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
     * Set numeroPlazas
     *
     * @param integer $numeroPlazas
     * @return Curso
     */
    public function setNumeroPlazas($numeroPlazas)
    {
        $this->numeroPlazas = $numeroPlazas;

        return $this;
    }

    /**
     * Get numeroPlazas
     *
     * @return integer 
     */
    public function getNumeroPlazas()
    {
        return $this->numeroPlazas;
    }

    /**
     * Set numeroPlazasPrioritarias
     *
     * @param integer $numeroPlazasPrioritarias
     * @return Curso
     */
    public function setNumeroPlazasPrioritarias($numeroPlazasPrioritarias)
    {
        $this->numeroPlazasPrioritarias = $numeroPlazasPrioritarias;

        return $this;
    }

    /**
     * Get numeroPlazasPrioritarias
     *
     * @return integer 
     */
    public function getNumeroPlazasPrioritarias()
    {
        return $this->numeroPlazasPrioritarias;
    }

    /**
     * Set cursoAcademico
     *
     * @param \AppBundle\Entity\CursoAcademico $cursoAcademico
     * @return Curso
     */
    public function setCursoAcademico(\AppBundle\Entity\CursoAcademico $cursoAcademico = null)
    {
        $this->cursoAcademico = $cursoAcademico;

        return $this;
    }

    /**
     * Get cursoAcademico
     *
     * @return \AppBundle\Entity\CursoAcademico 
     */
    public function getCursoAcademico()
    {
        return $this->cursoAcademico;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->matriculas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add matriculas
     *
     * @param \AppBundle\Entity\Matricula $matriculas
     * @return Curso
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
     * Add preinscripciones
     *
     * @param \AppBundle\Entity\PreinscripcionEnCurso $preinscripciones
     * @return Curso
     */
    public function addPreinscripcione(\AppBundle\Entity\PreinscripcionEnCurso $preinscripciones)
    {
        $this->preinscripciones[] = $preinscripciones;

        return $this;
    }

    /**
     * Remove preinscripciones
     *
     * @param \AppBundle\Entity\PreinscripcionEnCurso $preinscripciones
     */
    public function removePreinscripcione(\AppBundle\Entity\PreinscripcionEnCurso $preinscripciones)
    {
        $this->preinscripciones->removeElement($preinscripciones);
    }

    /**
     * Get preinscripciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPreinscripciones()
    {
        return $this->preinscripciones;
    }

    /**
     * Set disciplina
     *
     * @param \AppBundle\Entity\Disciplina $disciplina
     * @return Curso
     */
    public function setDisciplina(\AppBundle\Entity\Disciplina $disciplina = null)
    {
        $this->disciplina = $disciplina;

        return $this;
    }

    /**
     * Get disciplina
     *
     * @return \AppBundle\Entity\Disciplina 
     */
    public function getDisciplina()
    {
        return $this->disciplina;
    }
}
