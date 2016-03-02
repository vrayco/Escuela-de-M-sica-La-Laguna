<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DisciplinaGrupo
 *
 * @ORM\Table(name="disciplina_grupo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DisciplinaGrupoRepository")
 * @UniqueEntity({"nombre"})
 */
class DisciplinaGrupo
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
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="")
     */
    private $nombre;

    /**
     * @ORM\Column(name="incompatible_con_otro", type="boolean")
     */
    private $incompatibleConOtro;

    /**
     * @ORM\Column(name="maximo_inscripciones", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 1
     * )
     */
    private $maximoInscripciones;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Disciplina", mappedBy="disciplinaGrupo", cascade={"persist","remove"})
     */
    private $disciplinas;

    public function __construct()
    {
        $this->disciplinas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->incompatibleConOtro = false;
        $this->maximoInscripciones = 1;
    }

    public function __toString()
    {
        return $this->nombre;
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
     * @return DisciplinaGrupo
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
     * Add disciplinas
     *
     * @param \AppBundle\Entity\Disciplina $disciplinas
     * @return DisciplinaGrupo
     */
    public function addDisciplina(\AppBundle\Entity\Disciplina $disciplinas)
    {
        $this->disciplinas[] = $disciplinas;

        return $this;
    }

    /**
     * Remove disciplinas
     *
     * @param \AppBundle\Entity\Disciplina $disciplinas
     */
    public function removeDisciplina(\AppBundle\Entity\Disciplina $disciplinas)
    {
        $this->disciplinas->removeElement($disciplinas);
    }

    /**
     * Get disciplinas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDisciplinas()
    {
        return $this->disciplinas;
    }

    /**
     * Set incompatibleConOtro
     *
     * @param boolean $incompatibleConOtro
     * @return DisciplinaGrupo
     */
    public function setIncompatibleConOtro($incompatibleConOtro)
    {
        $this->incompatibleConOtro = $incompatibleConOtro;

        return $this;
    }

    /**
     * Get incompatibleConOtro
     *
     * @return boolean 
     */
    public function getIncompatibleConOtro()
    {
        return $this->incompatibleConOtro;
    }

    /**
     * Set maximoInscripciones
     *
     * @param integer $maximoInscripciones
     * @return DisciplinaGrupo
     */
    public function setMaximoInscripciones($maximoInscripciones)
    {
        $this->maximoInscripciones = $maximoInscripciones;

        return $this;
    }

    /**
     * Get maximoInscripciones
     *
     * @return integer 
     */
    public function getMaximoInscripciones()
    {
        return $this->maximoInscripciones;
    }
}
