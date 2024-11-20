<?php

namespace App\Entity;

use App\Repository\MailAccountRepository;
use App\Validator\EmailMatchesDomain;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[EmailMatchesDomain]
#[ORM\Entity(repositoryClass: MailAccountRepository::class)]
class MailAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mailAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domain $domain = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'mailAccount', targetEntity: MailAlias::class)]
    private Collection $mailAliases;

    public function __construct()
    {
        $this->mailAliases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, MailAlias>
     */
    public function getMailAliases(): Collection
    {
        return $this->mailAliases;
    }

    public function addMailAlias(MailAlias $mailAlias): static
    {
        if (!$this->mailAliases->contains($mailAlias)) {
            $this->mailAliases->add($mailAlias);
            $mailAlias->setMailAccount($this);
        }

        return $this;
    }

    public function removeMailAlias(MailAlias $mailAlias): static
    {
        if ($this->mailAliases->removeElement($mailAlias)) {
            // set the owning side to null (unless already changed)
            if ($mailAlias->getMailAccount() === $this) {
                $mailAlias->setMailAccount(null);
            }
        }

        return $this;
    }

}
