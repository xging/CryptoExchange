<?php

namespace App\Command\Interfaces;

interface CommandArgsValidateInterface
{
    public function validate(string $argument): bool;
}
