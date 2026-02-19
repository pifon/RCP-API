<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_subscriptions')]
#[ORM\Entity]
class UserSubscription
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[ORM\JoinColumn(name: 'plan_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Plan $plan;

    #[ORM\Column(name: 'billing_cycle', type: 'string', length: 10, nullable: false, options: ['default' => 'monthly'])]
    private string $billingCycle = 'monthly';

    #[ORM\Column(name: 'status', type: 'string', length: 20, nullable: false, options: ['default' => 'active'])]
    private string $status = 'active';

    #[ORM\Column(name: 'external_id', type: 'string', length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(name: 'trial_ends_at', type: 'datetime', nullable: true)]
    private ?DateTime $trialEndsAt = null;

    #[ORM\Column(name: 'current_period_start', type: 'datetime', nullable: false)]
    private DateTime $currentPeriodStart;

    #[ORM\Column(name: 'current_period_end', type: 'datetime', nullable: false)]
    private DateTime $currentPeriodEnd;

    #[ORM\Column(name: 'cancelled_at', type: 'datetime', nullable: true)]
    private ?DateTime $cancelledAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function setPlan(Plan $plan): void
    {
        $this->plan = $plan;
    }

    public function getBillingCycle(): string
    {
        return $this->billingCycle;
    }

    public function setBillingCycle(string $cycle): void
    {
        $this->billingCycle = $cycle;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->updatedAt = new DateTime();
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $id): void
    {
        $this->externalId = $id;
    }

    public function getTrialEndsAt(): ?DateTime
    {
        return $this->trialEndsAt;
    }

    public function setTrialEndsAt(?DateTime $date): void
    {
        $this->trialEndsAt = $date;
    }

    public function getCurrentPeriodStart(): DateTime
    {
        return $this->currentPeriodStart;
    }

    public function setCurrentPeriodStart(DateTime $date): void
    {
        $this->currentPeriodStart = $date;
    }

    public function getCurrentPeriodEnd(): DateTime
    {
        return $this->currentPeriodEnd;
    }

    public function setCurrentPeriodEnd(DateTime $date): void
    {
        $this->currentPeriodEnd = $date;
    }

    public function getCancelledAt(): ?DateTime
    {
        return $this->cancelledAt;
    }

    public function cancel(): void
    {
        $this->cancelledAt = new DateTime();
        $this->status = 'cancelled';
        $this->updatedAt = new DateTime();
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing'], true)
            && $this->currentPeriodEnd >= new DateTime();
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing'
            && $this->trialEndsAt !== null
            && $this->trialEndsAt >= new DateTime();
    }
}
