<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DatecontrolValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new \UnexpectedValueException($value, 'DateTimeInterface');
        }
        $now = new \DateTime();
        $now->modify('-5 minutes');
        if ($value < $now) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
