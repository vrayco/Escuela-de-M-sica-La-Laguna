<?php
namespace AppBundle\Command;

use AppBundle\DataFixtures\ORM\LoadDisciplinaData;
use AppBundle\Entity\Alumno;
use AppBundle\Entity\Curso;
use AppBundle\Entity\CursoAcademico;
use AppBundle\Entity\Matricula;
use PHPExcel_Shared_Date;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CargarExcelCommand extends ContainerAwareCommand
{
    const FILENAME_EXPEDIENTES = "../DataFixtures/ficheros/exp.xls";
    const FILENAME_MATRICULAS = "../DataFixtures/ficheros/matricula.xls";

    private $alumnos = array();
    private $cursos = array();
    private $matriculas = array();

    private $logger = null;

    protected function configure()
    {
        $this
            ->setName('app:load:excel')
            ->setDescription('Carga datos desde excel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('monolog.logger.escuelademusica_sorteo');
        $now = new \DateTime('now');
        $this->logger->info(sprintf("INICIO de la carga %s", $now->format('d/m/Y H:i:s')));
        $validator = $this->getContainer()->get('validator');
        $this->inicializar($input, $output);
        $this->loadAlumnos($input, $output);
        $this->loadMatriculas($input, $output);

        $em = $this->getContainer()->get('doctrine')->getManager();
        foreach($this->alumnos as $a) {
            $errors = $validator->validate($a);
            if(count($errors) > 0) {
                $this->logger->error(sprintf("Errores de validación para Alumno %s - %s", $a->__toString(), $a->getObservaciones()));
                foreach($errors as $error)
                    $this->logger->error(sprintf("Error: %s", $error));
            }
            else {
                $a = $this->getContainer()->get('utils.expediente')->setExpediente($a);
                $em->persist($a);
                $em->flush();
            }
        }


        $now = new \DateTime('now');
        $this->logger->info(sprintf("FIN de la carga %s", $now->format('d/m/Y H:i:s')));
    }

    private function inicializar(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('d:f:l');

        $arguments = array(
            'command' => 'demo:greet',
            '-n'    => 'true',
        );

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);
        $this->logger->info(sprintf("FIN DE LA INICIALIZACION (CODE: ".$returnCode.")"));
    }

    private function loadAlumnos(InputInterface $input, OutputInterface $output)
    {
        $phpExcel = $this->getContainer()->get('phpexcel');

        $phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject(__DIR__.'/'.self::FILENAME_EXPEDIENTES);

        $salir = false;
        $index = 3;
        $blanco = 0;
        $contador = 0;
        while(!$salir) {
            if($phpExcelObject->getActiveSheet()->getCell("A".$index)->getValue() != "") {
                $blanco = 0;
                $alumno = new Alumno();
                $alumno = $this->getContainer()->get('utils.expediente')->setExpediente($alumno);
                $alumno->setNombre($phpExcelObject->getActiveSheet()->getCell("A" . $index)->getValue());
                $alumno->setApellidos($phpExcelObject->getActiveSheet()->getCell("B" . $index)->getValue());
                $alumno->setAnoIngreso($phpExcelObject->getActiveSheet()->getCell("C" . $index)->getValue());
                $observaciones = $phpExcelObject->getActiveSheet()->getCell("G" . $index)->getValue() . ' | Expediente anterior: ' . $phpExcelObject->getActiveSheet()->getCell("D" . $index)->getValue() . ' | Especialidad: ' . $phpExcelObject->getActiveSheet()->getCell("E" . $index)->getValue() . ' | Estado:' . $phpExcelObject->getActiveSheet()->getCell("F" . $index)->getValue();
                $alumno->setObservaciones($observaciones);

                $alumno->setDireccion('---');
                $alumno->setLocalidad('---');
                $alumno->setCodigoPostal('---');

                $this->alumnos[$phpExcelObject->getActiveSheet()->getCell("D" . $index)->getValue()] = $alumno;
                $index++;
                $contador++;
            } else {
                $index++;
                $blanco++;
                if($blanco > 3)
                    $salir = true;
            }
        }

        $this->logger->info("Alumnos leídos en hoja expedientes: ".$contador);
    }

    private function loadMatriculas(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $cursoAcademico = $em->getRepository('AppBundle:CursoAcademico')->findOneBy(array('nombre' => '2015-2016'));

        // D_MUSICA_MOVIMIENTO_1_1
        $index = 1;
        $nombreDisciplina = LoadDisciplinaData::D_MUSICA_MOVIMIENTO_1_1;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        // D_MUSICA_MOVIMIENTO_1_2
        $index = 2;
        $nombreDisciplina = LoadDisciplinaData::D_MUSICA_MOVIMIENTO_1_2;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        // D_MUSICA_MOVIMIENTO_2_1
        $index = 3;
        $nombreDisciplina = LoadDisciplinaData::D_MUSICA_MOVIMIENTO_2_1;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        // D_MUSICA_MOVIMIENTO_2_2
        $index = 4;
        $nombreDisciplina = LoadDisciplinaData::D_MUSICA_MOVIMIENTO_2_2;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 5;
        $nombreDisciplina = LoadDisciplinaData::D_VIOLIN;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 6;
        $nombreDisciplina = LoadDisciplinaData::D_FLAUTA;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 7;
        $nombreDisciplina = LoadDisciplinaData::D_BATERIA;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 8;
        $nombreDisciplina = LoadDisciplinaData::D_BAJO_MODERNO;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 9;
        $nombreDisciplina = LoadDisciplinaData::D_GUITARRA_MODERNA;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        // Pagina con actividades de conjunto pagina 10 (excel)

        $index = 11;
        $nombreDisciplina = LoadDisciplinaData::D_CLARINETE;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 12;
        $nombreDisciplina = LoadDisciplinaData::D_GUITARRA_CLASICA;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 13;
        $nombreDisciplina = LoadDisciplinaData::D_PERCUSION;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 14;
        $nombreDisciplina = LoadDisciplinaData::D_TROMPETA;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 15;
        $nombreDisciplina = LoadDisciplinaData::D_CELLO;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 16;
        $nombreDisciplina = LoadDisciplinaData::D_PIANO;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));

        $index = 17;
        $nombreDisciplina = LoadDisciplinaData::D_SAXOFON;
        $num = $this->loadCurso($cursoAcademico, $index, $nombreDisciplina);
        $this->logger->info(sprintf("%s - NÚM MATRICULA=%d ", $nombreDisciplina, $num));
    }

    private function loadCurso(CursoAcademico $cursoAcademico, $index, $nombreDisciplina)
    {
        $index = $index -1;
        $em = $this->getContainer()->get('doctrine')->getManager();
        $disciplina = $em->getRepository('AppBundle:Disciplina')->findOneBy(array(
            'nombre' => $nombreDisciplina
        ));
        $curso = $em->getRepository('AppBundle:Curso')->findOneBy(array(
            'disciplina'        => $disciplina,
            'cursoAcademico'    => $cursoAcademico
        ));

        $phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject(__DIR__.'/'.self::FILENAME_MATRICULAS);
        $phpExcelObject->setActiveSheetIndex($index);
        dump($phpExcelObject->getActiveSheet()->getCell("a1")->getValue(). " INDEX:".$index);
        $salir = false;
        $index = 3;
        $blanco = 0;
        $contador = 0;
        while(!$salir) {
            if($phpExcelObject->getActiveSheet()->getCell("B".$index)->getValue() != "") {
                $idExp = str_replace(' ', '', $phpExcelObject->getActiveSheet()->getCell("F" . $index)->getValue());
                if(!isset($this->alumnos[$idExp])) {
                    $this->logger->error("Alumno no encontrado exp: ".$phpExcelObject->getActiveSheet()->getCell("F" . $index)->getValue());
                } else {
                    $alumno = $this->alumnos[$idExp];

                    $fechaNacimiento    = $phpExcelObject->getActiveSheet()->getCell("C" . $index)->getValue();
                    $telefono           = $phpExcelObject->getActiveSheet()->getCell("D" . $index)->getValue();
                    $email              = $phpExcelObject->getActiveSheet()->getCell("E" . $index)->getValue();
                    date($format = "Y/m/d", PHPExcel_Shared_Date::ExcelToPHP($fechaNacimiento));
                    $InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($fechaNacimiento));
                    $alumno->setFechaNacimiento(new \DateTime($InvDate));
                    $alumno->setTelefonoMovil($telefono);
                    $alumno->setEmail($email);

                    $matricula = new Matricula();
                    $matricula->setPrefijo($cursoAcademico->getPrefijoExpediente());
                    $matricula->setCurso($curso);
                    $matricula->setAlumno($alumno);
                    $alumno->addMatricula($matricula);
                    $this->alumnos[$phpExcelObject->getActiveSheet()->getCell("F" . $index)->getValue()] = $alumno;
                    $contador++;
                }
                $index++;
            } else {
                $index++;
                $blanco++;
                if($blanco > 3)
                    $salir = true;
            }
        }

        return $contador;
    }
}