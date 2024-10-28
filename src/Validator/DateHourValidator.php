<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DateHourValidator
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $date
     * @param string $hour
     * @return array
     */
    public function validate(string $date, string $hour): array
    {
        $errors         = [];
        $dateConstraint = new Assert\Date();
        $dateViolations = $this->validator->validate($date, $dateConstraint);

        if (count($dateViolations) > 0) {
            $errors[] = 'Invalid date format, expected YYYY-MM-DD.';
        }

        if (!preg_match('/^(2[0-3]|[01]?[0-9])$/', $hour)) {
            $errors[] = 'Invalid hour format, expected HH between 00 and 23.';
        }

        if (empty($errors)) {
            $providedDateTime = \DateTimeImmutable::createFromFormat('Y-m-d H', $date . ' ' . $hour);
            $currentDateTime  = new \DateTimeImmutable();

            if ($providedDateTime >= $currentDateTime) {
                $errors[] = 'The provided date and time must be in the past.';
            }
        }

        return $errors;
    }
}
