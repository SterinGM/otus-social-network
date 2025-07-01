<?php

namespace App\Service\ErrorSystem;

interface Translatable
{
    public function translateCode(): string;
}