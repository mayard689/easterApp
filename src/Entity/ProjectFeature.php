<?php

namespace App\Entity;

use App\Repository\ProjectFeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjectFeatureRepository::class)
 * @Table(name="project_feature",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="name_project",
 *            columns={"name", "project_id"})
 *    }
 * )
 * @Entity
 * @UniqueEntity(
 *      fields={"name","project"},
 *      message="Cette fonctionnalité existe déjà dans le projet."
 * )
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
     *     message="La valeur {{value}} n'est pas de type {{type}}."
     * )
     * @Assert\PositiveOrZero
     *
     */
    private $day;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(
     *     message = "La description ne doit pas être vide"
     * )
     *
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

     * @ORM\ManyToOne(targetEntity=Category::class)
     *
     * @assert\Valid()
     * @Assert\NotBlank
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isHigh;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMiddle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLow;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @assert\Valid()
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?float
    {
        return $this->day;
    }

    public function setDay(?float $day): self
    {
        $this->day = $day;

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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

    public function getIsHigh(): ?bool
    {
        return $this->isHigh;
    }

    public function setIsHigh(bool $isHigh): self
    {
        $this->isHigh = $isHigh;

        return $this;
    }

    public function getIsMiddle(): ?bool
    {
        return $this->isMiddle;
    }

    public function setIsMiddle(bool $isMiddle): self
    {
        $this->isMiddle = $isMiddle;

        return $this;
    }

    public function getIsLow(): ?bool
    {
        return $this->isLow;
    }

    public function setIsLow(bool $isLow): self
    {
        $this->isLow = $isLow;

        return $this;
    }

    public function getSelectVariant()
    {
        if ($this->isLow === false && $this->isMiddle === false && $this->isHigh === false) {
            return false;
        }
        return true;
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
}
