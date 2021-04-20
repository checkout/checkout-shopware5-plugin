<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CardManagement;

use CkoCheckoutPayment\Components\Structs\CardStruct;
use CkoCheckoutPayment\Models\StoredCard;
use Doctrine\ORM\QueryBuilder;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Customer\Customer;

class CardManagementService implements CardManagementServiceInterface
{
    /**
     * @var ModelManager
     */
    private $modelManager;
	/**
	 * @var QueryBuilder
	 */
	private $queryBuilder;

	public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
		$this->queryBuilder = $modelManager->createQueryBuilder();
	}

    public function saveCard(CardStruct $cardStruct): void
    {
        $storedCardRepo = $this->modelManager->getRepository(StoredCard::class);
        $storedCard = $storedCardRepo->findOneBy([
            'sourceId' => $cardStruct->getSourceId(),
            'customer' => $cardStruct->getCustomerId(),
        ]);
        if($storedCard)  {
            return;
        }
        $card = new StoredCard();
        $card->setCustomer($this->getCustomerById($cardStruct->getCustomerId()));
        $card->setSourceId($cardStruct->getSourceId());
        $card->setLastFour($cardStruct->getLastFour());
        $card->setExpiryMonth($cardStruct->getExpiryMonth());
        $card->setExpiryYear($cardStruct->getExpiryYear());
        $card->setScheme($cardStruct->getScheme());

        $this->modelManager->persist($card);
        $this->modelManager->flush();
    }

    public function getCards($customerId): array
    {
        $cardRepository = $this->modelManager->getRepository(StoredCard::class);
        return $cardRepository->findBy(['customer' => $customerId]);
    }

    public function deleteCard(int $customerId, $sourceId): void
    {
        $cardRepository = $this->modelManager->getRepository(StoredCard::class);

        $card = $cardRepository->findOneBy(['customer' => $customerId, 'sourceId' => $sourceId]);
        if ($card === null) {
            throw new \Exception(sprintf('Card with id %d not found.', $sourceId));
        }

        $this->modelManager->remove($card);
        $this->modelManager->flush();
    }

	public function deleteAllCards(): void
	{
		$this->queryBuilder->delete(StoredCard::class, 'cr')->getQuery()->execute();
	}

    private function getCustomerById(int $customerId): Customer
    {
        $customerRepository = $this->modelManager->getRepository(Customer::class);

        /** @var Customer $customer */
        $customer = $customerRepository->findOneBy(['id' => $customerId]);
        if ($customer === null) {
            throw new \Exception(sprintf('Customer with id %d not found.', $customerId));
        }

        return $customer;
    }
}
