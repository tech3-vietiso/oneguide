<?php

namespace Vietiso\OneGuide\Tour;

class Operator
{
    private ?string $name = null;

    private ?string $phone = null;

    private ?string $email = null;

    private ?string $avatar = null;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
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

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;
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