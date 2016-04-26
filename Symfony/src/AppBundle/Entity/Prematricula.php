<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppBundleAssert;

/**
 * Prematricula
 *
 * @ORM\Table(name="prematricula")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrematriculaRepository")
 *
 * * @UniqueEntity({"alumno","prefijo"},
 *    errorPath="alumno",
 *    message="Existe una pre-matrícula para este alumno."
 * )
 *
 * @AppBundleAssert\Prematricula()
 */
class Prematricula
{
    const NUM_CURSOS = 3;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Alumno", inversedBy="prematriculas")
     * @ORM\JoinColumn(name="alumno_id", referencedColumnName="id")
     */
    private $alumno;

    /**
     * @ORM\Column(name="prefijo", type="string", length=2)
     */
    private $prefijo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PrematriculaEnCurso", mappedBy="prematricula", cascade={"persist","remove"})
     * @Assert\Valid
     */
    private $prematriculaEnCursos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->prematriculaEnCursos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdentificador()
    {
        return 'PRE_MA-'.$this->prefijo.$this->id;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Prematricula
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Prematricula
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set alumno
     *
     * @param \AppBundle\Entity\Alumno $alumno
     * @return Prematricula
     */
    public function setAlumno(\AppBundle\Entity\Alumno $alumno = null)
    {
        $this->alumno = $alumno;

        return $this;
    }

    /**
     * Get alumno
     *
     * @return \AppBundle\Entity\Alumno 
     */
    public function getAlumno()
    {
        return $this->alumno;
    }

    /**
     * Add prematriculaEnCursos
     *
     * @param \AppBundle\Entity\PrematriculaEnCurso $prematriculaEnCursos
     * @return Prematricula
     */
    public function addPrematriculaEnCurso(\AppBundle\Entity\PrematriculaEnCurso $prematriculaEnCursos)
    {
        $this->prematriculaEnCursos[] = $prematriculaEnCursos;

        return $this;
    }

    /**
     * Remove prematriculaEnCursos
     *
     * @param \AppBundle\Entity\PrematriculaEnCurso $prematriculaEnCursos
     */
    public function removePrematriculaEnCurso(\AppBundle\Entity\PrematriculaEnCurso $prematriculaEnCursos)
    {
        $this->prematriculaEnCursos->removeElement($prematriculaEnCursos);
    }

    /**
     * Get prematriculaEnCursos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrematriculaEnCursos()
    {
        return $this->prematriculaEnCursos;
    }

    /**
     * Set prefijo
     *
     * @param string $prefijo
     * @return Prematricula
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

    public function getCursoAcademico()
    {
        if(count($this->prematriculaEnCursos) > 0)
            return $this->prematriculaEnCursos[0]->getCurso()->getCursoAcademico();
        else
            return null;
    }
}
