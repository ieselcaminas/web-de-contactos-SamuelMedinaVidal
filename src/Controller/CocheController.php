<?php

namespace App\Controller;

use App\Entity\Coche;
use App\Entity\Propietario;
use App\Form\CocheFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CocheController extends AbstractController
{
    
    private $coches = [
        1 => ["marca" => "Toyota", "modelo" => "Corolla", "matricula" => "1234-ABC", "color" => "Blanco"],
        2 => ["marca" => "Seat", "modelo" => "Ibiza", "matricula" => "5678-DEF", "color" => "Rojo"],
        3 => ["marca" => "Tesla", "modelo" => "Model 3", "matricula" => "9012-GHI", "color" => "Negro"],
        4 => ["marca" => "Ford", "modelo" => "Focus", "matricula" => "3456-JKL", "color" => "Azul"],
        5 => ["marca" => "Hyundai", "modelo" => "Tucson", "matricula" => "7890-MNP", "color" => "Gris"]
    ];
    

    #[Route('/coche/insertar', name: 'insertar_coche')]
    public function insertar(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        foreach($this->coches as $c){
            $coche = new Coche();
            $coche->setMarca($c["marca"]);
            $coche->setModelo($c["modelo"]);
            $coche->setMatricula($c["matricula"]);
            $coche->setColor($c["color"]);
            $entityManager->persist($coche);
        }

        try
        {
            //Sólo se necesita realizar flush una vez y confirmará todas las operaciones pendientes
            $entityManager->flush();
            return new Response("Coches insertados");
        } catch (\Exception $e) {
            return new Response("Error insertando objetos");
        }
    }
    
    
    #[Route('/coche/insertar-con-propietario', name: 'con_propietario')]
    public function insertarConPropietario(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $propietario = new Propietario();
        $propietario->setNombre("Juanjo Vidal");
        $propietario->setDni("26598548Z");
        $propietario->setTelefono("642988372");

        $coche = new Coche();
        $coche->setMarca("Ford");
        $coche->setModelo("Focus");
        $coche->setMatricula("6789-ATC");
        $coche->setColor("Azul");
        
        $coche->setPropietario($propietario);

        $entityManager->persist($propietario);
        $entityManager->persist($coche);
        $entityManager->flush();

        return new Response("Coche y propietario insertados con éxito");
    }
    

    /*
    #[Route('/coche/{matricula}', name: 'ficha_coche')]
    public function ficha(ManagerRegistry $doctrine, $matricula): Response
    {
        $repositorio = $doctrine->getRepository(Coche::class);
        $coche = $repositorio->find($matricula);
        
        return $this->render('ficha_coche.html.twig', ["coche" => $coche]);
    }    
    
    */
    
    #[Route('/coche/update/{matricula}/{color}', name: 'modificar_color')]
    public function update(ManagerRegistry $doctrine, $matricula, $color) : Response
    {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Coche::class);
        $coche = $repositorio->find($matricula);
        if ($coche){
            $coche->setColor($color);
            try{
                $entityManager->flush();
                return $this->render('ficha_coche.html.twig', ['coche' => $coche]);
            } catch (\Exception $e){
                return new Response("Error insertando objetos");
            }
        } else{
            return $this->render('ficha_coche.html.twig', ['coche' => null]);
        
        }
    }
    

    #[Route('/coche/buscar/{modelo}', name: 'buscar_coche')]
    public function buscar(ManagerRegistry $doctrine, $modelo): Response
    {
        $repositorio = $doctrine->getRepository(Coche::class);
        
        $coches = $repositorio->findByName($modelo);

        return $this->render('buscar_coche.html.twig', ['coches' => $coches]);
    }

    #[Route('/coche/delete/{matricula}', name: 'eliminar_coche')]
    public function delete(ManagerRegistry $doctrine, $matricula) : Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Coche::class);
        $contacto = $repositorio->find($matricula);
        if ($contacto){
            try
            {
                $entityManager->remove($contacto);
                $entityManager->flush();
                return new Response("Contacto eliminado");
            } catch (\Exception $e){
                return new Response("Error eliminando objeto");
            }
        } else{
            return $this->render('ficha_contacto.html.twig', ['contacto' => null]);
        }
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/coche/nuevo', name: 'nuevo_coche')]
    public function nuevo(ManagerRegistry $doctrine, Request $request) : Response
    {
        $coche = new Coche();
        $form = $this->createForm(CocheFormType::class, $coche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($coche);
            $entityManager->flush();

            return $this->redirectToRoute('ficha_coche', ['matricula' => $coche->getMatricula()]);
        }

        return $this->render('coche/nuevo.html.twig', ['formulario' => $form->createView()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/coche/editar/{matricula}', name: 'editar_coche')]
    public function editar(ManagerRegistry $doctrine, Request $request, string $matricula): Response
    {
        $repositorio = $doctrine->getRepository(Coche::class);
        $coche = $repositorio->find($matricula);

        if ($coche) {
            $formulario = $this->createForm(CocheFormType::class, $coche);
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($coche);
                $entityManager->flush();

                return $this->redirectToRoute('ficha_coche', ["matricula" => $coche->getMatricula()]);
            }

            return $this->render('/coche/nuevo.html.twig', [
                'formulario' => $formulario->createView()
            ]);
        } else {
            return $this->render('ficha_coche.html.twig', [
                'coche' => null
            ]);
        }
    }
}