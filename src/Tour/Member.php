<?php

namespace Vietiso\OneGuide\Tour;

use Vietiso\OneGuide\Arrayable;
use DateTimeInterface;
use Vietiso\OneGuide\Support;

class Member implements Arrayable
{
    private ?string $id = null;

    private ?string $fullName = null;

    private ?int $gender = null;

    private ?DateTimeInterface $birthday = null;

    private ?string $phone = null;

    private ?string $email = null;

    private ?string $passportNumber = null;

    private ?DateTimeInterface $passportExpiryDate = null;

    private ?string $identityCardNumber = null;

    private ?string $countryId = null;

    private ?string $note = null;

    public function setId(string $id): self
    {
        $this->id = Support::trimString($id);
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = Support::trimString($fullName);
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setBirthday(DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function getBirthday(): ?DateTimeInterface
    {
        return $this->birthday;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = Support::trimString($phone);
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setEmail(string $email): self
    {
        $this->email = Support::trimString($email);
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassportNumber(string $passportNumber): self
    {
        $this->passportNumber = Support::trimString($passportNumber);
        return $this;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportExpiryDate(DateTimeInterface $passportExpiryDate): self
    {
        $this->passportExpiryDate = $passportExpiryDate;
        return $this;
    }

    public function getPassportExpiryDate(): ?DateTimeInterface
    {
        return $this->passportExpiryDate;
    }

    /**
     * Số CCCD (hoặc CMND).
     */
    public function setIdentityCardNumber(string $identityCardNumber): self
    {
        $this->identityCardNumber = Support::trimString($identityCardNumber);
        return $this;
    }

    public function getIdentityCardNumber(): ?string
    {
        return $this->identityCardNumber;
    }

    public function setCountryId(string $countryId): self
    {
        $this->countryId = Support::trimString($countryId);
        return $this;
    }

    public function getCountryId(): ?string
    {
        return $this->countryId;
    }

    public function setNote(string $note): self
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
        $birthday = $this->getBirthday();
        $passportExpiryDate = $this->getPassportExpiryDate();

        return [
            'external_guest_id' => $this->getId(),
            'full_name' => $this->getFullName(),
            'birthday' => $birthday instanceof DateTimeInterface ? $birthday->format('Y-m-d') : null,
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'passport_number' => $this->getPassportNumber(),
            'passport_expiry_date' => $passportExpiryDate instanceof DateTimeInterface
                ? $passportExpiryDate->format('Y-m-d')
                : null,
            'identity_card_number' => $this->getIdentityCardNumber(),
            'gender' => $this->getGender(),
            'country_id' => $this->getCountryId(),
            'note' => $this->getNote(),
        ];
    }
}
