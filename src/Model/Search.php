<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Search
{
    const INTERVAL = 12;
    
    const FROM_TIMES = [
        '06:00' => '06:00',
        '12:00' => '12:00',
    ];
    
    const TO_TIMES = [
        '12:00' => '12:00',
        '18:00' => '18:00',
    ];
    
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $fromDate = null;
    
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::FROM_TIMES)]
    private ?string $fromTime = null;
    
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $toDate = null;
    
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::TO_TIMES)]
    private ?string $toTime = null;
    
    public function __construct()
    {
        $this->fromDate = (new \DateTimeImmutable())->modify('+1 day')->setTime(0, 0);
        $this->fromTime = self::FROM_TIMES['06:00'];
        
        $this->toDate   = (new \DateTimeImmutable())->modify('+2 day')->setTime(0, 0);
        $this->toTime   = self::TO_TIMES['12:00'];
    }
    
    public function getFromDate(): ?\DateTimeImmutable
    {
        return $this->fromDate;
    }
    
    public function setFromDate(?\DateTimeImmutable $fromDate): Search
    {
        $this->fromDate = $fromDate;
        return $this;
    }
    
    public function getFromTime(): ?string
    {
        return $this->fromTime;
    }
    
    public function setFromTime(?string $fromTime): Search
    {
        $this->fromTime = $fromTime;
        return $this;
    }
    
    public function getFromDateTime()
    {
        return $this->fromDate->setTime(...explode(':', $this->fromTime));
    }
    
    public function getToDate(): ?\DateTimeImmutable
    {
        return $this->toDate;
    }
    
    public function setToDate(?\DateTimeImmutable $toDate): Search
    {
        $this->toDate = $toDate;
        return $this;
    }
    
    public function getToTime(): ?string
    {
        return $this->toTime;
    }
    
    public function setToTime(?string $toTime): Search
    {
        $this->toTime = $toTime;
        return $this;
    }
    
    public function getToDateTime()
    {
        return $this->toDate->setTime(...explode(':', $this->toTime));
    }
    
    public function getDurationInDays(): float|int
    {
        // 12 hours = 0.5 days
        // 24 hours = 1 day
        // 36 hours = 1.5 days
        return $this->getFromDateTime()->diff($this->getToDateTime())->d + ($this->getFromDateTime()->diff($this->getToDateTime())->h / 12);
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        // End date must be greater than start date
        if ($this->getFromDateTime() >= $this->getToDateTime()) {
            $context->buildViolation('End date must be greater than start date')
                ->atPath('fromDate')
                ->addViolation()
            ;
        }
        
        // Start date must be at least tomorrow
        if ($this->getFromDate() < (new \DateTimeImmutable())->modify('+1 day')->setTime(0, 0)) {
            $context->buildViolation('Start date must be at least tomorrow')
                ->atPath('fromDate')
                ->addViolation()
            ;
        }
    }
}