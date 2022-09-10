<?php


namespace App\Interface;


interface Serializable extends \JsonSerializable
{
    public function jsonSerialize(): array;
}