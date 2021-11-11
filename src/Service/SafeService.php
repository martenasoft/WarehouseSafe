<?php

namespace MartenaSoft\WarehouseSafe\Service;

use App\Repository\SafeRepository;
use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\WarehouseReports\Entity\Operation;

class SafeService
{
    private EntityManagerInterface $entityManager;
    private SafeRepository $safeRepository;

    public function __construct(EntityManagerInterface $entityManager, SafeRepository $safeRepository)
    {
        $this->entityManager = $entityManager;
        $this->safeRepository = $safeRepository;
    }

    public function income(
        float $sum,
        ?float $oldSum = 0,
        string $name = Operation::ADD_TO_SAFE_NAME,
        string $description = Operation::ADD_TO_SAFE_DESCRIPTION
    ): void {

        try {
            $this->entityManager->beginTransaction();
            if ($oldSum === 0) {
                $oldSum = $this->safeRepository->getLastSum();
            }

            $name = sprintf($name, $sum, $oldSum);
            $description = sprintf($description, $sum, $oldSum);

            $this->save($sum, Operation::TYPE_ADD, $name, $description);
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
        }
    }

    public function withdraw(
        float $sum,
        ?float $oldSum = 0,
        string $name = Operation::ADD_TO_SAFE_NAME,
        string $description = Operation::CHANGED_IN_SAFE_DESCRIPTION
    ): void {
        try {
            $this->entityManager->beginTransaction();
            $oldSum = $this->safeRepository->getLastSum();
            if ($oldSum === 0) {
                $oldSum = $this->safeRepository->getLastSum();
            }

            $name = sprintf($name, $sum, $oldSum);
            $description = sprintf($description, $sum, $oldSum);

            $this->save($sum, Operation::TYPE_DEDUCT, $name, $description);
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
        }
    }


    public function save(float $sum, int $type, string $name, string $description): void
    {
        $Operation = new Operation();
        $Operation
            ->setName($name)
            ->setType($type)
            ->setDatetime(new \DateTime('now'))
            ->setDescription($description)
            ->setSum($sum)
        ;

        $this->entityManager->persist($Operation);
        $this->entityManager->flush();
    }
}