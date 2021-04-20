<?php declare(strict_types=1);

namespace CkoCheckoutPayment\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_plugin_cko_events", uniqueConstraints={
 *     @UniqueConstraint(
 *      name="event_unique",
 *          columns={"event_id"})
 * })
 */
class Event extends ModelEntity
{
    /**
     * @var int $id
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_processed")
     */
    protected $isProcessed;

    /**
     * @ORM\Column(type="string", nullable=false, length=40, name="event_id")
     */
    protected $eventId;

    /**
     * @ORM\Column(type="string", nullable=false, length=40, name="payment_id")
     */
    protected $paymentId;

    /**
     * @ORM\Column(type="string", nullable=false, length=20, name="type")
     */
    protected $type;

    /**
     * @ORM\Column(type="datetimetz", nullable=false, name="created_on")
     */
    protected $createdOn;

    /**
     * @ORM\Column(type="text", nullable=false, name="data")
     */
    protected $data;

    public function __construct()
    {
        $this->isProcessed = false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIsProcessed()
    {
        return $this->isProcessed;
    }

    /**
     * @param bool $isProcessed
     */
    public function setIsProcessed(bool $isProcessed): void
    {
        $this->isProcessed = $isProcessed;
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param mixed $eventId
     */
    public function setEventId($eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     */
    public function setPaymentId($paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn): void
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}

