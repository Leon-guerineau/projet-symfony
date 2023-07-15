<?php
namespace App\Data;

class PostSearch
{

    private $title;

    private $author;

    private $maxDate;

    private $minDate;

    private $games = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMaxDate(): ?\DateTime
    {
        return $this->maxDate;
    }

    public function setMaxDate(?\DateTime $maxDate): self
    {
        $this->maxDate = $maxDate;

        return $this;
    }

    public function getMinDate(): ?\DateTime
    {
        return $this->minDate;
    }

    public function setMinDate(?\DateTime $minDate): self
    {
        $this->minDate = $minDate;

        return $this;
    }

    public function getGames(): ?array
    {
        return $this->games;
    }

    public function setGames(?array $games): self
    {
        $this->games = $games;

        return $this;
    }


}

