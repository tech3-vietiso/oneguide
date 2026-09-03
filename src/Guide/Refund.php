<?php

namespace Vietiso\OneGuide\Guide;

use Vietiso\OneGuide\Arrayable;
use Vietiso\OneGuide\Support;

/**
 * Khoản hoàn tạm ứng: số tiền hướng dẫn viên trả lại cho điều hành sau khi quyết toán tour.
 */
class Refund implements Arrayable
{
    private ?string $id = null;

    private ?string $code = null;

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
            'amount' => $this->getAmount(),
            'currency_code' => $this->getCurrency(),
            'title' => $this->getTitle(),
            'note' => $this->getNote(),
        ];
    }
}
