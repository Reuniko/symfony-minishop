<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\Table(name: "carts")]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deliveryServiceId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $paymentServiceId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deliveryPrice = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deliveryMinDays = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deliveryMaxDays = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $isPay;

    #[ORM\Column(type: 'integer')]
    private int $totalPaymentSum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDeliveryServiceId(): ?int
    {
        return $this->deliveryServiceId;
    }

    public function setDeliveryServiceId(?int $deliveryServiceId): static
    {
        $this->deliveryServiceId = $deliveryServiceId;

        return $this;
    }

    public function getDeliveryPrice(): ?string
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(?string $deliveryPrice): static
    {
        $this->deliveryPrice = $deliveryPrice;

        return $this;
    }

    public function getDeliveryMinDays(): ?int
    {
        return $this->deliveryMinDays;
    }

    public function setDeliveryMinDays(?int $deliveryMinDays): static
    {
        $this->deliveryMinDays = $deliveryMinDays;

        return $this;
    }

    public function getDeliveryMaxDays(): ?int
    {
        return $this->deliveryMaxDays;
    }

    public function setDeliveryMaxDays(?int $deliveryMaxDays): static
    {
        $this->deliveryMaxDays = $deliveryMaxDays;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isPay(): ?bool
    {
        return $this->isPay;
    }

    public function setPay(bool $isPay): static
    {
        $this->isPay = $isPay;

        return $this;
    }

    public function getTotalPaymentSum(): ?string
    {
        return $this->totalPaymentSum;
    }

    public function setTotalPaymentSum(?string $totalPaymentSum): static
    {
        $this->totalPaymentSum = $totalPaymentSum;

        return $this;
    }
}
