<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Datecontrol extends Constraint
{
    public $message = 'La date ne peut pas être dans le passé.';
}