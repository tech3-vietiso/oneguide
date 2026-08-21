<?php

namespace Vietiso\OneGuide\Guide;

use Vietiso\OneGuide\Arrayable;
use Vietiso\OneGuide\Support;

class Advance implements Arrayable
{
    private ?string $id = null;

    private ?string $code = null;

    private ?string $serviceId = null;

    private ?float $amount = null;

    private ?string $currencyCode = 'VND';

    private ?string $note = null;

    private ?string $title = null;

    public function setId(string $id)
    {
        $this->id = Support::trimString($id);
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setCode(string $code)
    {
        $this->code = Support::trimString($code);
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setTitle(string $title)
    {
        $this->title = Support::trimString($title);
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setServiceId(string $serviceId)
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setCurrency(string $currencyCode)
    {
        $this->currencyCode = Support::trimString($currencyCode);
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currencyCode;
    }

    public function setNote(string $note)
    {
        $this->note = Support::trimString($note);
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function toArray(): array
    {
        return [
            'external_expense_id' => $this->getId(),
            'expense_code' => $this->getCode(),
            'tour_service_id' => $this->getServiceId(),
            'amount' => $this->getAmount(),
            'currency_code' => $this->getCurrency(),
            'title' => $this->getTitle(),
            'note' => $this->getNote(),
        ];
    }
}