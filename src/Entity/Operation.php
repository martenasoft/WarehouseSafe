<?php

namespace MartenaSoft\WarehouseSafe\Entity;

use MartenaSoft\WarehouseCommon\Entity\Traits\DescriptionTrait;
use MartenaSoft\WarehouseCommon\Entity\Traits\NameTrait;
use MartenaSoft\WarehouseSafe\Repository\OperationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OperationRepository::class)
 */
class Operation
{
    use NameTrait, DescriptionTrait;

    public const TYPE_ADD = 1;
    public const TYPE_DEDUCT = 2;

    public const ADD_TO_SAFE_NAME = 'Add to safe';
    public const ADD_TO_SAFE_DESCRIPTION = 'It was added to safe %d sum (old sum: %d)';

    public const CHANGED_IN_SAFE_NAME = 'Changed in safe';
    public const CHANGED_IN_SAFE_DESCRIPTION = 'It was changed safe %d sum (old sum: %d)';
    public const DEDUCT_FROM_SAFE_NAME = 'Deduct from safe';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $sum;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): self
    {
        $this->sum = $sum;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
