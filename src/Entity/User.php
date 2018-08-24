<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="Il existe deja un utlisateur avec cet email")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(max="100", maxMessage="Le nom ne doit pas faire plus de {{limit}} caractères ")
     *
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Le pré est obligatoire")
     * @Assert\Length(max="100", maxMessage="Le prénom ne doit pas faire plus de {{limit}} caractères ")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $password;

    /**
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le pré est obligatoire")
     * @Assert\Email(message="L'email n'est pas valide"))
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $role ='ROLE_USER';

    /**
     * Mot de passe en clair pour interagir avec le formulaire
     * @var string
     * @assert\NotBlank(message="Le mot de passe est obligatoire")
     */
    private $plainPassword;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Transforme un objet user en chaîne de caractères normée
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize(
            [
          $this ->id,
          $this->lastname,
          $this->firstname,
          $this->email,
          $this->password
            ]
        );
    }

    /**
     * Transforme uneen chaîne de caractères par un objet User
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this ->id,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->password
            ) = unserialize($serialized);
    }

    /**
     * Role sous forme d'un array
     * @return array
     */
    public function getRoles()
    {
        return [$this->role];
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * Quel attribut va servir d'identifiant
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {

    }

    public function __toString(){
        return$this->firstname. ' ' . $this->lastname;
    }

}
