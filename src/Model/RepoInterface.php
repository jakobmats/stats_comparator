<?php

namespace App\Model;

/**
 * Represents a repo and its associated data
 */
interface RepoInterface
{

    /**
     * Gets repo name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets repo owner's name
     *
     * @return string
     */
    public function getUserName(): string;

    /**
     * Gets repo data bag
     *
     * @return array
     */
    public function getDataBag(): array;
}