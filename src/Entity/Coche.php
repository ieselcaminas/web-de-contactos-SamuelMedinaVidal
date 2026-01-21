<?php

namespace App\Entity;

use Assert\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CocheRepository;

#[ORM\Entity(repositoryClass: CocheRepository::class)]
class Coche
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La marca no puede estar vacía")]
    private ?string $marca = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"El modelo es obligatorio")]
    private ?string $modelo = null;

    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $matricula = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"El color se debe introducir")]
    private ?string $color = null;

    #[ORM\ManyToOne(inversedBy: 'coches')]
    #[ORM\JoinColumn(nullable:false)]
    #[Assert\NotBlank(message: "Debes asignar un propietario")]
    private ?Propietario $propietario = null;

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getMatricula(): ?string
    {
        return $this->matricula;
    }

    public function setMatricula(string $matricula): self
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPropietario(): ?Propietario
    {
        return $this->propietario;
    }

    public function setPropietario(?Propietario $propietario): self
    {
        $this->propietario = $propietario;

        return $this;
    }
}
