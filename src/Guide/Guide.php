<?php

namespace Vietiso\OneGuide\Guide;

use Vietiso\OneGuide\Arrayable;

class Guide implements Arrayable
{
    private string $numberCard;

    private ?string $email = null;

    private ?string $phone = null;

    public function __construct(string $numberCard)
    {
        $this->numberCard = $numberCard; 
    }

    public function setNumberCard(string $numberCard): self
    {
        $this->numberCard = $numberCard;
        return $this;
    }

    public function getNumberCard(): string
    {
        return $this->numberCard;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function toArray(): array
    {
        return [
            'number_card' => $this->getNumberCard(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail()
        ];
    }
}