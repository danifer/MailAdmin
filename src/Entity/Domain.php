<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
class Domain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $domainName = null;

    /**
     * @var Collection<int, MailAccount>
     */
    #[ORM\OneToMany(targetEntity: MailAccount::class, mappedBy: 'domain')]
    private Collection $mailAccounts;

    public function __construct()
    {
        $this->mailAccounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomainName(): ?string
    {
        return $this->domainName;
    }

    public function setDomainName(string $domainName): static
    {
        $this->domainName = $domainName;

        return $this;
    }

    /**
     * @return Collection<int, MailAccount>
     */
    public function getMailAccounts(): Collection
    {
        return $this->mailAccounts;
    }

    public function addMailAccount(MailAccount $mailAccount): static
    {
        if (!$this->mailAccounts->contains($mailAccount)) {
            $this->mailAccounts->add($mailAccount);
            $mailAccount->setDomain($this);
        }

        return $this;
    }

    public function removeMailAccount(MailAccount $mailAccount): static
    {
        if ($this->mailAccounts->removeElement($mailAccount)) {
            // set the owning side to null (unless already changed)
            if ($mailAccount->getDomain() === $this) {
                $mailAccount->setDomain(null);
            }
        }

        return $this;
    }
}
