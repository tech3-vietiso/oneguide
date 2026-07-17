<?php

namespace Vietiso\OneGuide\Tour;

use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Arrayable;

class Itinerary implements Arrayable
{
    private string $title;

    private int $dayNumber;

    private ?string $image = null;

    private array $guides = [];

    private array $provinces = [];

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDayNumber(int $dayNumber): self
    {
        $this->dayNumber = $dayNumber;
        return $this;
    }

    public function getDayNumber(): int
    {
        return $this->dayNumber;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function addGuide(Guide $guide): self
    {
        $this->guides[] = $guide;
        return $this;
    }

    public function getGuides(): array
    {
        return $this->guides;
    }

    public function setProvince(string $provinceCode): self
    {
        $this->provinces[] = $provinceCode;
        return $this;
    }

    public function getProvinces(): array
    {
        return $this->provinces;
    }

    public function toArray(): array
    {
        $guides = array_map(static function ($guide) {
            return $guide->toArray();
        }, $this->getGuides());

        return [
            'title' => $this->getTitle(),
            'day_number' => $this->getDayNumber(),
            'image' => $this->getImage(),
            'guides' => $guides,
            'provinces' => $this->getProvinces()
        ];
    }
}