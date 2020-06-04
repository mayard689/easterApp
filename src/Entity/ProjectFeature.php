<?php

namespace App\Entity;

use App\Repository\ProjectFeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjectFeatureRepository::class)
 */
class ProjectFeature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="float",
     *     message="La valeur {{ value }} n'est pas de type {{ type }}."
     * )
     * @Assert\PositiveOrZero
     *
     */
    private $day;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="projectFeatures")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=Feature::class, inversedBy="projectFeatures")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank
     */
    private $feature;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?float
    {
        return $this->day;
    }

    public function setDay(float $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getFeature(): ?Feature
    {
        return $this->feature;
    }

    public function setFeature(?Feature $feature): self
    {
        $this->feature = $feature;

        return $this;
    }
}
