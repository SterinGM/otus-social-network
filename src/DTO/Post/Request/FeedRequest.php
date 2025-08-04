<?php

namespace App\DTO\Post\Request;

class FeedRequest
{
    public const string FIELD_OFFSET = 'offset';
    public const string FIELD_LIMIT = 'limit';

    public int $offset;
    public int $limit;
    public string $userId;

    private function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_OFFSET] ?? 0,
            $data[self::FIELD_LIMIT] ?? 0,
        );
    }
}