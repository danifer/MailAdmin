<?php

namespace App\Entity;

use App\Repository\MailAliasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;

#[ORM\Entity(repositoryClass: MailAliasRepository::class)]
class MailAlias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[App\Validator\SourceEmailDomainExists]
    private ?string $source = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $destination = null;


    public function getId(): ?int
    {
        return $this->id;
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
        $destinations = array_map('trim', explode(',', $destination));
        $destinations = array_map('strtolower', $destinations);
        $destinations = array_filter(filter_var_array($destinations, FILTER_VALIDATE_EMAIL));

        $this->destination = implode(',', $destinations);

        return $this;
    }

    public function removeFromDestination(string $string): static
    {
        $string = trim($string);
        $destinations = array_map('trim', explode(',', $this->getDestination()));
        $this->setDestination(
            implode(
                ',',
                array_diff(
                    $destinations, [$string]
                )
            )
        );

        return $this;
    }
}
