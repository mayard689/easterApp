<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte avec cet e-mail")
 */
class User implements UserInterface
{
    const ROLES_AVAILABLE = [
        'Utilisateur' => 'ROLE_APPUSER',
        'Administrateur' => 'ROLE_ADMIN'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *     message="L'adresse email doit être renseignée",
     *     groups={"User","UserUpdate"}
     * )
     * @Assert\Length(
     *     max= 180,
     *     maxMessage="L\'adresse email ne doit pas dépassée les {{ limit }} caractères",
     *     groups={"User","UserUpdate"}
     * )
     * @Assert\Email(
     *     message="L'adresse mail saisie n'est pas une adresse mail valide",
     *     groups={"User","UserUpdate"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank(
     *     message="Le rôles doit être renseigné",
     *     groups={"UpdateUser"}
     *)
     * @Assert\All({
     *     @Assert\Choice(
     *     choices=User::ROLES_AVAILABLE,
     *     message="{{ choices }} {{ value }}",
     *     groups={"UpdateUser"}
     *     )
     * })
     * @Assert\Count(
     *     max="1",
     *     maxMessage="L'utilisateur ne peut avoir qu'un seul rôle",
     *     groups={"User", "UpdateUser"}
     * )
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="Merci de saisir un mot de passe",
     *     groups={"Password"}
     * )
     * @Assert\NotNull(
     *     message="Merci de saisir un mot de passe",
     *     groups={"Password"}
     * )
     * @Assert\Length(
     *     min="8",
     *     minMessage="Le mot de passe doit contenir minimum 8 caractères",
     *     groups={"Pasword"}
     * )
     * @Assert\Regex(
     *     pattern="/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$/",
     *     message="Le mot de passe doit contenir au minimum 1 chiffre, 1 majuscule, et un caractère spécial.",
     *     groups={"Password"}
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(
     *     message="Le nom doit être renseigné",
     *     groups={"User","UserUpdate"}
     * )
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Le nom ne doit pas dépassé les {{ limit }} caractères",
     *     groups={"User","UserUpdate"}
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(
     *     message="Le prénom doit être renseigné",
     *     groups={"User","UserUpdate"}
     * )
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Le prénom ne doit pas dépassé les {{ limit }} caractères",
     *     groups={"User","UserUpdate"}
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="date")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePicture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }
}
