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
     * @ORM\Column(name="entra_en_sorteo", type="boolean")
     */
    private $entraEnSorteo;

    /**
     * @ORM\Column(name="entra_en_sorteo_prematricula", type="boolean")
     */
    private $entraEnSorteoPrematricula;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroPlazasPrematricula", type="integer", nullable=true)
     * @Assert\NotBlank(message="")
     * @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     */
    private $numeroPlazasPrematricula;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroPlazas", type="integer", nullable=true)
     * @Assert\NotBlank(message="")
     * @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     */
    private $numeroPlazas;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroPlazasPrioritarias", type="integer", nullable=true)
     * @Assert\NotBlank(message="")
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

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PrematriculaEnCurso", mappedBy="curso", cascade={"persist","remove"})
     */
    private $prematriculas;

    public function __toString()
    {
        return $this->disciplina->getNombre() . ' (' .$this->disciplina->getDisciplinaGrupo()->getNombre(). ')';
    }
    
    public function __construct()
    {
        $this->matriculas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->preinscripciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->prematriculas = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->entraEnSorteoPrematricula = true;
        $this->entraEnSorteo = true;
        $this->numeroPlazasPrematricula = 0;
        $this->numeroPlazas = 0;
        $this->numeroPlazasPrioritarias = 0;
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

    /**
     * Set entraEnSorteo
     *
     * @param boolean $entraEnSorteo
     * @return Curso
     */
    public function setEntraEnSorteo($entraEnSorteo)
    {
        $this->entraEnSorteo = $entraEnSorteo;

        return $this;
    }

    /**
     * Get entraEnSorteo
     *
     * @return boolean 
     */
    public function getEntraEnSorteo()
    {
        return $this->entraEnSorteo;
    }

    /**
     * Add prematriculas
     *
     * @param \AppBundle\Entity\PrematriculaEnCurso $prematriculas
     * @return Curso
     */
    public function addPrematricula(\AppBundle\Entity\PrematriculaEnCurso $prematriculas)
    {
        $this->prematriculas[] = $prematriculas;

        return $this;
    }

    /**
     * Remove prematriculas
     *
     * @param \AppBundle\Entity\PrematriculaEnCurso $prematriculas
     */
    public function removePrematricula(\AppBundle\Entity\PrematriculaEnCurso $prematriculas)
    {
        $this->prematriculas->removeElement($prematriculas);
    }

    /**
     * Get prematriculas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrematriculas()
    {
        return $this->prematriculas;
    }

    /**
     * Set numeroPlazasPrematricula
     *
     * @param integer $numeroPlazasPrematricula
     * @return Curso
     */
    public function setNumeroPlazasPrematricula($numeroPlazasPrematricula)
    {
        $this->numeroPlazasPrematricula = $numeroPlazasPrematricula;

        return $this;
    }

    /**
     * Get numeroPlazasPrematricula
     *
     * @return integer 
     */
    public function getNumeroPlazasPrematricula()
    {
        return $this->numeroPlazasPrematricula;
    }

    /**
     * Set entraEnSorteoPrematricula
     *
     * @param boolean $entraEnSorteoPrematricula
     * @return Curso
     */
    public function setEntraEnSorteoPrematricula($entraEnSorteoPrematricula)
    {
        $this->entraEnSorteoPrematricula = $entraEnSorteoPrematricula;

        return $this;
    }

    /**
     * Get entraEnSorteoPrematricula
     *
     * @return boolean 
     */
    public function getEntraEnSorteoPrematricula()
    {
        return $this->entraEnSorteoPrematricula;
    }
}
