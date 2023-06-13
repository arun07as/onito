<?php

namespace App\Entities;

use DateTime;

class MovieData
{
    public function __construct(
        private ?int $id,
        private ?string $tconst,
        private string $titleType,
        private string $primaryTitle,
        private int $runtimeMinutes,
        private string $genres,
        private ?DateTime $createdAt = null,
        private ?DateTime $updatedAt = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTconst(): ?string
    {
        return $this->tconst;
    }

    public function getTitleType(): string
    {
        return $this->titleType;
    }

    public function getPrimaryTitle(): string
    {
        return $this->primaryTitle;
    }

    public function getRuntimeMinutes(): int
    {
        return $this->runtimeMinutes;
    }

    public function getGenres(): string
    {
        return $this->genres;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
