<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class EmployeeValidator
{
    /**
     * @param array
     * @return array
     */
    public function validate(array $data): array
    {
        $validator = Validation::createValidator();
        
        $constraints = new Assert\Collection([
            'firstName' => [
                new Assert\NotBlank(['message' => 'Imie nie może być puste']),
                new Assert\Length([
                    'min' => 1,
                    'max' => 50,
                    'minMessage' => 'Imie musi zawierać conajmniej {{ limit }} znak',
                    'maxMessage' => 'Imie nie może być dłuższe niż {{ limit }} znaków'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s-]+$/',
                    'message' => 'Imie może zawierać tylko litery, spacje i myślniki'
                ])
            ],
            'surname' => [
                new Assert\NotBlank(['message' => 'Nazwisko nie może być puste']),
                new Assert\Length([
                    'min' => 1,
                    'max' => 50,
                    'minMessage' => 'Nazwisko musi zawierać conajmniej {{ limit }} znak',
                    'maxMessage' => 'Nazwisko nie może być dłuższe niż {{ limit }} znaków'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s-]+$/',
                    'message' => 'Nazwisko może zawierać tylko litery, spacje i myślniki'
                ])
            ]
        ], allowExtraFields: true);
        
        $violations = $validator->validate($data, $constraints);
        
        if (count($violations) === 0) {
            return [];
        }
        
        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }
        
        return $errors;
    }
}