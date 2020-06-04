<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom du projet ne doit pas dépasser {{ limit }} caractères",
     *      min=1,
     *      minMessage = "Le nom du projet doit faire au moins 1 caractère",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\LessThanOrEqual(100, message="La valeur de 'expert' ne doit pas excéder {{ compared_value }}")
     * @Assert\GreaterThanOrEqual(0, message="La valeur de 'expert' doit être supérieure à {{ compared_value }}")
     */
    private $expert;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\LessThanOrEqual(100, message="La valeur de 'confirmé' ne doit pas excéder {{ compared_value }}")
     * @Assert\GreaterThanOrEqual(0, message="La valeur de 'confirmé' doit être supérieure à {{ compared_value }}")
     */
    private $confirmed;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\LessThanOrEqual(100, message="La valeur de 'junior' ne doit pas excéder {{ compared_value }}")
     * @Assert\GreaterThanOrEqual(0, message="La valeur de 'junior' doit être supérieure à {{ compared_value }}")
     */
    private $junior;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="projects")
     *
     * @Assert\Valid
     */
    private $application;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getExpert(): ?int
    {
        return $this->expert;
    }

    public function setExpert(int $expert): self
    {
        $this->expert = $expert;

        return $this;
    }

    public function getConfirmed(): ?int
    {
        return $this->confirmed;
    }

    public function setConfirmed(int $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getJunior(): ?int
    {
        return $this->junior;
    }

    public function setJunior(int $junior): self
    {
        $this->junior = $junior;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}
