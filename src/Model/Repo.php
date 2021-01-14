<?php

namespace App\Model;

class Repo implements RepoInterface
{
    private string $name;
    private string $userName;
    private array $dataBag;

    public function __construct(string $userName, string $name, array $dataBag)
    {
        $this->userName = $userName;
        $this->name = $name;
        $this->dataBag = $dataBag;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDataBag(): array
    {
        return $this->dataBag;
    }
}