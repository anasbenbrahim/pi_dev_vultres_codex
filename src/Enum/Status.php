<?php

namespace App\Enum;

enum Status: string
{
    case EN_ATTENTE = 'enAttente';
    case EN_COURS = 'enCours';
    case TERMINE = 'termine';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'Pending',
            self::EN_COURS => 'In Progress',
            self::TERMINE => 'Completed',
        };
    }
}