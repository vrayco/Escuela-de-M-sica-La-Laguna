<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disciplina
 *
 * @ORM\Table(name="disciplina")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DisciplinaRepository")
 */
class Disciplina
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="edadMinima", type="integer", nullable=true)
     */
    private $edadMinima;

    /**
     * @var int
     *
     * @ORM\Column(name="edadMaxima", type="integer", nullable=true)
     */
    private $edadMaxima;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DisciplinaGrupo", inversedBy="disciplinas")
     * @ORM\JoinColumn(name="disciplina_grupo_id", referencedColumnName="id")
     */
    private $disciplinaGrupo;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Curso", mappedBy="disciplina", cascade={"persist","remove"})
     */
    private $cursos;

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
     * @return Disciplina
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
     * Set edadMinima
     *
     * @param integer $edadMinima
     * @return Disciplina
     */
    public function setEdadMinima($edadMinima)
    {
        $this->edadMinima = $edadMinima;

        return $this;
    }

    /**
     * Get edadMinima
     *
     * @return integer 
     */
    public function getEdadMinima()
    {
        return $this->edadMinima;
    }

    /**
     * Set edadMaxima
     *
     * @param integer $edadMaxima
     * @return Disciplina
     */
    public function setEdadMaxima($edadMaxima)
    {
        $this->edadMaxima = $edadMaxima;

        return $this;
    }

    /**
     * Get edadMaxima
     *
     * @return integer 
     */
    public function getEdadMaxima()
    {
        return $this->edadMaxima;
    }

    /**
     * Set disciplinaGrupo
     *
     * @param \AppBundle\Entity\DisciplinaGrupo $disciplinaGrupo
     * @return Disciplina
     */
    public function setDisciplinaGrupo(\AppBundle\Entity\DisciplinaGrupo $disciplinaGrupo = null)
    {
        $this->disciplinaGrupo = $disciplinaGrupo;

        return $this;
    }

    /**
     * Get disciplinaGrupo
     *
     * @return \AppBundle\Entity\DisciplinaGrupo 
     */
    public function getDisciplinaGrupo()
    {
        return $this->disciplinaGrupo;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cursos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cursos
     *
     * @param \AppBundle\Entity\Curso $cursos
     * @return Disciplina
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
}
