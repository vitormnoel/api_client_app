<?php

namespace App\Interface;

interface EntityInterface extends \JsonSerializable
{
    public function jsonSerialize(): array;
}