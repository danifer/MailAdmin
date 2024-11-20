<?php

namespace App\Entity;

use App\Repository\MailAliasRepository;
use App\Validator\EmailMatchesDomain;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[EmailMatchesDomain]
#[ORM\Entity(repositoryClass: MailAliasRepository::class)]
class MailAlias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mailAliases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domain $domain = null;

    #[ORM\ManyToOne(inversedBy: 'mailAliases')]
    private ?MailAccount $mailAccount = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $source = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $destination = null;

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

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getMailAccount(): ?MailAccount
    {
        return $this->mailAccount;
    }

    public function setMailAccount(?MailAccount $mailAccount): static
    {
        $this->mailAccount = $mailAccount;

        return $this;
    }
}
