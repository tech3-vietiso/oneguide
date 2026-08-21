<?php

namespace Vietiso\OneGuide\Tour;

use Vietiso\OneGuide\Support;

class Operator
{
    private ?string $name = null;

    private ?string $phone = null;

    private ?string $email = null;

    private ?string $avatar = null;

    public function setName(string $name): self
    {
        $this->name = Support::trimString($name);
        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
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

    public function setAvatar(string $avatar): self
    {
        $this->avatar = Support::trimString($avatar);
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'avatar' => $this->getAvatar(),
        ];
    }
}