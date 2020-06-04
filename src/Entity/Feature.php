<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FeatureRepository::class)
 */
class Feature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez indiquer un nom pour cette fonctionnalité !")
     * @Assert\Length(
     *     max=255,
     *     maxMessage="Votre nom de fonctionnalité ne doit pas dépasser {{ limit }} caractères !"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Veuillez indiquer un datage pour cette fonctionnalité !")
     * @Assert\Positive(message="Le datage doit être supérieur à 0 !")
     */
    private $day;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez indiquer une description pour cette fonctionnalité !")
     * @Assert\Length(
     *     max=255,
     *     maxMessage="La description ne doit pas dépasser {{ limit }} caractères ! "
     * )
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="feature")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
