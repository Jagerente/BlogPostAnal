<?php

namespace App\Entity;

use App\Enum\PostEventsEnum;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PostAnalytics implements ISerializable
{
    /**
     * @SerializedName("user_id")
     */
    private int $userId;

    /**
     * @SerializedName("post_id")
     */
    private int $postId;


    /**
     * @SerializedName("event")
     */
    private PostEventsEnum $event;

    /**
     * @SerializedName("details")
     */
    private string $details;

    /**
     * @SerializedName("created_at")
     */
    private \DateTimeImmutable $createdAt;

    public function __construct(
    ) {
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    public function getSerialized(): string
    {
        return json_encode($this->getValuesArray());
    }

    public function getValuesArray(): array
    {
        $data = [
            'user_id' => $this->getUserId(),
            'post_id' => $this->getPostId(),
            'event' => $this->getEvent()->value,
            'details' => $this->getDetails(),
            'created_at' => $this->getCreatedAtFormated(),
        ];

        return $data;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getEvent(): PostEventsEnum
    {
        return $this->event;
    }

    public function setEvent(PostEventsEnum $event): void
    {
        $this->event = $event;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function setDetails(string $details): void
    {
        $this->details = $details;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAtFormated(): string
    {
        return $this->createdAt->format('Y-m-d\TH:i:sP');
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}