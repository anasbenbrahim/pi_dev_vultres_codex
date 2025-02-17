<?php

namespace App\Enum;

enum EventType: string
{
    case SIMPLE = 'simple';
    case WORKSHOP = 'workshop';
    case FESTIVAL = 'festival';
}