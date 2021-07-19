<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"},message="Cet username n'est pas disponible !")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * Assert\Length(min=4 ,minMessage="Ce nom d'utilisteur est trop court")
     */
    private $password;



    /**
     * @ORM\OneToMany(targetEntity=Telephone::class, mappedBy="user", orphanRemoval=true)
     */
    private $telephones;

    public function __construct()
    {
        $this->telephones = new ArrayCollection();
    }

    /**
     * @Assert\EqualTo(propertyPath="password",message"Les mots de passe ne corresponent pas !")
     * @Assert\Length(min=4 max=16 ,minMessage="Ce mot de passe est trop court")
     */
    private $passwordConfirm;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    public function getRoles()
    {
        return ['ROLE_USER'];
        // TODO: Implement getRoles() method.
    }
    public function getUserIdentifier(): string
    {
        return $this->username;
        // TODO: Implement getUserIdentifier() method.
    }

    /**
     * @return Collection|Telephone[]
     */
    public function getTelephones(): Collection
    {
        return $this->telephones;
    }

    public function addTelephone(Telephone $telephone): self
    {
        if (!$this->telephones->contains($telephone)) {
            $this->telephones[] = $telephone;
            $telephone->setUser($this);
        }

        return $this;
    }

    public function removeTelephone(Telephone $telephone): self
    {
        if ($this->telephones->removeElement($telephone)) {
            // set the owning side to null (unless already changed)
            if ($telephone->getUser() === $this) {
                $telephone->setUser(null);
            }
        }

        return $this;
    }
}
