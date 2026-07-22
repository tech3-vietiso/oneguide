<?php

namespace Vietiso\OneGuide\Tour;

use Vietiso\OneGuide\Exception\ValidationException;
use Vietiso\OneGuide\Service\Service;
use Vietiso\OneGuide\Tour\Operator;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Client;
use DateTimeInterface;

class Tour
{
    private ?string $id = null;

    private ?string $title = null;

    private ?string $code = null;

    private ?int $type = null;

    private ?string $image = null;

    private ?DateTimeInterface $startDate = null;

    private float $numberDay = 0;

    private float $numberNight = 0;

    private int $numberAdult = 0;

    private int $numberChildren = 0;

    private array $itineraries = [];

    private array $operators = [];

    private array $guides = [];

    private array $services = [];

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
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

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
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

    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setNumberDay(float $numberDay): self
    {
        $this->numberDay = $numberDay;
        return $this;
    }

    public function getNumberDay(): float
    {
        return $this->numberDay;
    }

    public function setNumberNight(float $numberNight): self
    {
        $this->numberNight = $numberNight;
        return $this;
    }

    public function getNumberNight(): float
    {
        return $this->numberNight;
    }

    public function setNumberAdult(int $numberAdult): self
    {
        $this->numberAdult = $numberAdult;
        return $this;
    }

    public function getNumberAdult(): int
    {
        return $this->numberAdult;
    }

    public function setNumberChildren(int $numberChildren): self
    {
        $this->numberChildren = $numberChildren;
        return $this;
    }

    public function getNumberChildren(): int
    {
        return $this->numberChildren;
    }

    public function addItinerary(Itinerary $itinerary): self
    {
        $this->itineraries[] = $itinerary;
        return $this;
    }

    public function getItineraries(): array
    {
        return $this->itineraries;
    }

    public function addOperator(Operator $operator): self
    {
        $this->operators[] = $operator;
        return $this;
    }

    public function getOperators(): array
    {
        return $this->operators;
    }

    public function addGuide(Guide $guide, array $days = []): self
    {
        if (!empty($days)) {
            $guide->setDays($days);
        }
        $this->guides[] = $guide;
        return $this;
    }

    public function getGuides(): array
    {
        return $this->guides;
    }

    public function addService(Service $service): self
    {
        $this->services[] = $service;
        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function sync()
    {
        $this->validateSyncTour();

        $itineraries = array_map(static function ($itinerary) {
            return $itinerary->toArray();
        }, $this->getItineraries());

        $operators = array_map(static function ($operator) {
            return $operator->toArray();
        }, $this->getOperators());

        $services = array_map(static function ($service) {
            return $service->toArray();
        }, $this->getServices());

        $tour = [
            'external_tour_id' => $this->getId(),
            'name' => $this->getTitle(),
            'tour_code' => $this->getCode(),
            'tour_type' => $this->getType(),
            'image' => $this->getImage(),
            'start_date' => $this->getStartDate()->format('Y-m-d'),
            'days' => $this->getNumberDay(),
            'nights' => $this->getNumberNight(),
            'number_adult' => $this->getNumberAdult(),
            'number_children' => $this->getNumberChildren(),
            'itineraries' => $itineraries,
            'operators' => $operators,
            'services' => $services
        ];
        $this->client->send('integration-tours', $tour);
    }

    public function syncGuides()
    {
        $guides = array_map(static function ($guide) {
            return $guide->toArray();
        }, $this->getGuides());

        $this->validateSyncGuide($guides);

        $this->client->send("integration-tours/{$this->getId()}/sync-guides", [
            'guides' => $guides
        ]);
    }

    public function syncAdvances()
    {
        $this->validateSyncAdvances();

        $guides = array_map(static function ($guide) {
            $guide = $guide->toArray();

            if (!empty($guide['expenses'])) {
                $guide['expenses'] = array_map(static function ($advance) {
                    return $advance->toArray();
                }, $guide['expenses']);
            }

            return array_filter($guide);
        }, $this->getGuides());

        $this->client->send("integration-tours/{$this->getId()}/sync-advances", [
            'guides' => $guides
        ]);
    }

    private function validateSyncTour(): void
    {
        if (empty($this->id)) {
            throw new ValidationException('Tour: id is required.');
        }

        if (empty($this->title)) {
            throw new ValidationException('Tour: title is required.');
        }

        if (empty($this->code)) {
            throw new ValidationException('Tour: code is required.');
        }

        if (empty($this->type)) {
            throw new ValidationException('Tour: type is required.');
        }

        if (!in_array($this->type, [TourType::PRIVATE, TourType::SIC, TourType::OUTBOUND], true)) {
            throw new ValidationException('Tour: type is invalid.');
        }

        if (!$this->startDate instanceof DateTimeInterface) {
            throw new ValidationException('Tour: start date is required.');
        }

        if ($this->numberDay <= 0) {
            throw new ValidationException('Tour: number day is required.');
        }

        if ($this->numberAdult <= 0) {
            throw new ValidationException('Tour: number of adults must be greater than 0.');
        }

        if ($this->numberChildren < 0) {
            throw new ValidationException('Tour: number of children must be greater than or equal to 0.');
        }

        if (empty($this->itineraries)) {
            throw new ValidationException('Tour: at least one itinerary is required.');
        }

        if (empty($this->operators)) {
            throw new ValidationException('Tour: at least one operator is required.');
        }

        $this->assertImageUrl($this->image, 'Tour');

        foreach ($this->itineraries as $itinerary) {
            $this->assertImageUrl($itinerary->getImage(), 'Itinerary');
        }

        foreach ($this->operators as $operator) {
            if (empty($operator->getName())) {
                throw new ValidationException('Operator: name is required.');
            }

            $this->assertImageUrl($operator->getAvatar(), 'Operator');
            $this->assertPhoneLength($operator->getPhone(), 'Operator');
        }

        foreach ($this->services as $service) {
            $this->assertService($service);
        }
    }

    private function assertService(Service $service): void
    {
        $title = $service->getTitle();

        if (empty($service->getId())) {
            throw new ValidationException('Service: id is required.');
        }

        if (empty($title)) {
            throw new ValidationException('Service: title is required.');
        }

        if (empty($service->getType())) {
            throw new ValidationException("Service \"{$title}\": type is required.");
        }

        $days = $service->getTourDays();

        if (empty($days)) {
            throw new ValidationException("Service \"{$title}\": days is required.");
        }

        foreach ($days as $day) {
            if (!is_int($day) || $day < 1) {
                throw new ValidationException("Service \"{$title}\": day \"{$day}\" is invalid.");
            }

            if ($day > $this->numberDay) {
                throw new ValidationException(
                    "Service \"{$title}\": day \"{$day}\" must not exceed the tour's number of days ({$this->numberDay})."
                );
            }
        }
    }

    private function assertImageUrl(?string $image, string $label): void
    {
        if (!empty($image) && !filter_var($image, FILTER_VALIDATE_URL)) {
            throw new ValidationException("{$label}: image \"{$image}\" must be a valid URL.");
        }
    }

    private function assertPhoneLength(?string $phone, string $label): void
    {
        if (!empty($phone) && mb_strlen($phone) > 20) {
            throw new ValidationException("{$label}: phone must not exceed 20 characters.");
        }
    }

    private function validateSyncGuide(array $guides): void
    {
        foreach ($guides as $guide) {
            $cardNumber = $guide['card_number'] ?? null;
            $email = $guide['email'] ?? null;
            $phone = $guide['phone'] ?? null;

            if (empty($email)) {
                throw new ValidationException("Guide with number card \"{$cardNumber}\": email is required.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException("Guide with number card \"{$cardNumber}\": email \"{$email}\" is invalid.");
            }

            $this->assertPhoneLength($phone, "Guide with number card \"{$cardNumber}\"");
        }
    }

    private function validateSyncAdvances(): void
    {
        foreach ($this->getGuides() as $guide) {
            $cardNumber = $guide->getCardNumber();

            if (empty($cardNumber)) {
                throw new ValidationException('Guide: card number is required.');
            }

            $advances = $guide->getAdvances();

            if (empty($advances)) {
                throw new ValidationException("Guide with card number \"{$cardNumber}\": at least one advance is required.");
            }

            foreach ($advances as $advance) {
                if (empty($advance->getId())) {
                    throw new ValidationException("Guide with card number \"{$cardNumber}\": advance external_expense_id is required.");
                }

                if (empty($advance->getCode())) {
                    throw new ValidationException("Guide with card number \"{$cardNumber}\": advance expense_code is required.");
                }

                if (empty($advance->getServiceId())) {
                    throw new ValidationException("Guide with card number \"{$cardNumber}\": advance tour_service_id is required.");
                }

                if (empty($advance->getAmount())) {
                    throw new ValidationException("Guide with card number \"{$cardNumber}\": advance amount is required.");
                }
            }
        }
    }
}