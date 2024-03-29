<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{
    Column,
    Entity,
    GeneratedValue,
    HasLifecycleCallbacks,
    Id,
    OneToMany,
    PrePersist,
    PreUpdate,
    Table
};
use Exception;

#[Entity]
#[Table(name: 'events')]
#[HasLifecycleCallbacks]
class Event
{
    #[Id, GeneratedValue, Column]
    private ?int $id = null;

    #[Column(
        name: 'name',
        type: Types::STRING
    )]
    private string $name;

    #[Column(
        name: 'venue_name',
        type: Types::STRING,
        nullable: true
    )]
    private ?string $venueName;

    #[Column(
        name: 'external_venue_id',
        type: Types::STRING,
        nullable: true
    )]
    private ?string $externalVenueId;

    #[Column(
        name: 'attendance_cap',
        type: Types::INTEGER,
        nullable: true
    )]
    private ?int $attendanceCap;

    #[Column(
        name: 'ticket_cost_in_cents',
        type: Types::INTEGER,
        nullable: true
    )]
    private int $ticketCostInCents;

    #[Column(
        name: 'started_at',
        type: Types::DATETIME_MUTABLE,
        nullable: true
    )]
    private ?DateTime $startedAt;

    #[Column(
        name: 'ended_at',
        type: Types:: DATETIME_MUTABLE,
        nullable: true
    )]
    private ?DateTime $endedAt;

    #[Column(
        name: 'created_at',
        type: Types::DATETIME_MUTABLE
    )]
    private ?DateTime $createdAt = null;

    #[Column(
        name: 'updated_at',
        type: Types::DATETIME_MUTABLE
    )]
    private ?DateTime $updatedAt = null;

    #[Column(
        name: 'deleted_at',
        type: Types::DATETIME_MUTABLE,
        nullable: true
    )]
    private DateTime $deletedAt;

    #[OneToMany(
        mappedBy: 'event',
        targetEntity: 'Ticket',
        cascade: ['persist']
    )]
    private ?Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Event
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVenueName(): ?string
    {
        return $this->venueName;
    }

    /**
     * @param string|null $venueName
     */
    public function setVenueName(?string $venueName): void
    {
        $this->venueName = $venueName;
    }

    /**
     * @return string|null
     */
    public function getExternalVenueId(): ?string
    {
        return $this->externalVenueId;
    }

    /**
     * @param string|null $externalVenueId
     */
    public function setExternalVenueId(?string $externalVenueId): void
    {
        $this->externalVenueId = $externalVenueId;
    }

    /**
     * @return int|null
     */
    public function getAttendanceCap(): ?int
    {
        return $this->attendanceCap;
    }

    /**
     * @param int|null $attendanceCap
     * @return Event
     */
    public function setAttendanceCap(?int $attendanceCap): self
    {
        $this->attendanceCap = $attendanceCap;
        return $this;
    }

    /**
     * @return float
     */
    public function getTicketCostInCents(): float
    {
        return $this->ticketCostInCents / 100;
    }

    /**
     * @param float $ticketCostInCents
     * @return Event
     */
    public function setTicketCostInCents(float $ticketCostInCents): self
    {
        $this->ticketCostInCents = (int) ($ticketCostInCents * 100);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartedAt(): DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param DateTime|string|null $startedAt
     * @return Event
     * @throws Exception
     */
    public function setStartedAt(DateTime|string|null $startedAt): self
    {
        if (is_string($startedAt)) {
            $startedAt = new DateTime($startedAt);
        }
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndedAt(): DateTime
    {
        return $this->endedAt;
    }

    /**
     * @param DateTime|string|null $endedAt
     * @return Event
     * @throws Exception
     */
    public function setEndedAt(DateTime|string|null $endedAt): self
    {
        if (is_string($endedAt)) {
            $endedAt = new DateTime($endedAt);
        }
        $this->endedAt = $endedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function getEventDate(): array
    {
        return [
            'start' => $this->getStartedAt(),
            'end' => $this->getEndedAt()
        ];
    }

    public function setEventDate(array $eventDate): self
    {
        $this->setStartedAt($eventDate['start']);
        $this->setEndedAt($eventDate['end']);

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getName() . ': ' . $this->getStartedAt()->format('Y-m-d H:i:s') . ' - ' . $this->getEndedAt()->format('Y-m-d H:i:s');
    }

    /**
     * @return Collection
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * @param Collection $tickets
     */
    public function setTickets(Collection $tickets): void
    {
        $this->tickets = $tickets;
    }

    #[PrePersist, PreUpdate]
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new DateTime('now');

        $this->setUpdatedAt($dateTimeNow);

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    /**
     * @return ?DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param ?DateTime $createdAt
     * @return Event
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return Event
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime $deletedAt
     * @return Event
     */
    public function setDeletedAt(DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
