<?php

namespace App\Entity;

interface ISerializable
{
    function getSerialized(): string;

    function getValuesArray(): array;
}