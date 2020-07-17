<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @UniqueEntity("name",
 *               errorPath="name",
 *               message="Le nom de projet {{ value }} existe déjà. Veuillez en indiquer un autre.")
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
     * @Assert\NotBlank(message="Veuillez indiquer le nom du projet.")
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom du projet ne doit pas dépasser {{ limit }} caractères"
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
     * @Assert\NotBlank(message="Veuillez indiquer le pourcentage de ressources 'expertes' dans le projet.")
     * @Assert\LessThanOrEqual(100, message="La valeur de 'expert' ne doit pas excéder {{ compared_value }}")
     * @Assert\GreaterThanOrEqual(0, message="La valeur de 'expert' doit être supérieure à {{ compared_value }}")
     */
    private $expert;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank(message="Veuillez indiquer le pourcentage de ressources 'confirmées' dans le projet.")
     * @Assert\LessThanOrEqual(100, message="La valeur de 'confirmé' ne doit pas excéder {{ compared_value }}")
     * @Assert\GreaterThanOrEqual(0, message="La valeur de 'confirmé' doit être supérieure à {{ compared_value }}")
     */
    private $confirmed;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank(message="Veuillez indiquer le pourcentage de ressources 'juniors' dans le projet.")
     * @Assert\LessThanOrEqual(100, message="La valeur de 'junior' ne doit pas excéder {{ compared_value }}")
     * @Assert\GreaterThanOrEqual(0, message="La valeur de 'junior' doit être supérieure à {{ compared_value }}")
     */
    private $junior;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="projects")
     *
     * @Assert\NotBlank(message="Le type d'application choisi est invalide.")
     * @Assert\Valid
     */
    private $application;

    /**
     * @ORM\OneToMany(targetEntity=ProjectFeature::class, mappedBy="project", orphanRemoval=true)
     *
     * @Assert\Valid
     */
    private $projectFeatures;

    /**
     * @ORM\ManyToOne(targetEntity=Quotation::class, inversedBy="projects")
     * @Assert\NotBlank()
     */
    private $quotation;

    /**
     * @Assert\EqualTo(
     *     value=100,
     *     message="Le total des pourcentages doit être égal à 100%"
     * )
     * @return int
     */
    public function getTotalEmployee() :int
    {
        return $this->getJunior() + $this->getConfirmed() + $this->getExpert();
    }

    public function __construct()
    {
        $this->projectFeatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
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

    public function setExpert(?int $expert): self
    {
        $this->expert = $expert;

        return $this;
    }

    public function getConfirmed(): ?int
    {
        return $this->confirmed;
    }

    public function setConfirmed(?int $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getJunior(): ?int
    {
        return $this->junior;
    }

    public function setJunior(?int $junior): self
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

    /**
     * @return Collection|ProjectFeature[]
     */
    public function getProjectFeatures(): Collection
    {
        return $this->projectFeatures;
    }

    public function addProjectFeature(ProjectFeature $projectFeature): self
    {
        if (!$this->projectFeatures->contains($projectFeature)) {
            $this->projectFeatures[] = $projectFeature;
            $projectFeature->setProject($this);
        }

        return $this;
    }

    public function removeProjectFeature(ProjectFeature $projectFeature): self
    {
        if ($this->projectFeatures->contains($projectFeature)) {
            $this->projectFeatures->removeElement($projectFeature);
            // set the owning side to null (unless already changed)
            if ($projectFeature->getProject() === $this) {
                $projectFeature->setProject(null);
            }
        }

        return $this;
    }

    public function getQuotation(): ?Quotation
    {
        return $this->quotation;
    }

    public function setQuotation(?Quotation $quotation): self
    {
        $this->quotation = $quotation;

        return $this;
    }
}
