<?php

namespace Vietiso\OneGuide\Guide;

use Vietiso\OneGuide\Arrayable;

class Guide implements Arrayable
{
    private string $cardNumber;

    private ?string $email = null;

    private ?string $phone = null;

    private ?array $days = null;

    private ?array $advances = null;

    public function __construct(string $cardNumber)
    {
        $this->cardNumber = $cardNumber; 
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
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

    public function setDays(array $days): self
    {
        $this->days = $days;
        return $this;
    }

    public function getDays(): ?array
    {
        return $this->days;
    }

    public function addAdvance(Advance $advance)
    {
        $this->advances[] = $advance;
        return $this;
    }

    public function getAdvances(): ?array
    {
        return $this->advances;
    }

    public function toArray(): array
    {
        return [
            'card_number' => $this->getCardNumber(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'days' => $this->getDays(),
            'expenses' => $this->getAdvances(),
        ];
    }
}