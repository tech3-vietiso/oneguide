<?php

namespace Vietiso\OneGuide\Service;

use Vietiso\OneGuide\Arrayable;

class Service implements Arrayable
{
    private ?string $id = null;

    private ?string $title = null;

    private ?int $type = null;

    private ?string $address = null;

    private ?string $note = null;

    private ?array $days = null;

    private ?int $quantity = null;

    private ?float $amount = null;

    private ?string $companyName = null;

    private ?string $companyPhone = null;

    private ?string $companyEmail = null;

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setTourDays(array $days): self
    {
        $this->days = $days;
        return $this;
    }

    public function getTourDays(): ?array
    {
        return $this->days;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setCompanyName(string $companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyPhone(string $companyPhone)
    {
        $this->companyPhone = $companyPhone;
        return $this;
    }

    public function getCompanyPhone(): ?string
    {
        return $this->companyPhone;
    }

    public function setCompanyEmail(string $companyEmail)
    {
        $this->companyEmail = $companyEmail;
        return $this;
    }

    public function getCompanyEmail(): ?string
    {
        return $this->companyEmail;
    }

    public function setNote(string $note)
    {
        $this->note = $note;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function toArray(): array
    {
        return [
            'external_service_id' => $this->getId(),
            'title' => $this->getTitle(),
            'type' => $this->getType(),
            'address' => $this->getAddress(),
            'note' => $this->getNote(),
            'days' => $this->getTourDays(),
            'quantity' => $this->getQuantity(),
            'amount' => $this->getAmount(),
            'company_name' => $this->getCompanyName(),
            'company_phone' => $this->getCompanyPhone(),
            'company_email' => $this->getCompanyEmail(),
        ];
    }
}