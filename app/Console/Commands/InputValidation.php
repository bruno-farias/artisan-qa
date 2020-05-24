<?php


namespace App\Console\Commands;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

trait InputValidation
{
    protected function askValid(string $question, string $field, array $rules, string $locale): string
    {
        App::setLocale($locale);
        $value = $this->ask($question);

        if ($message = $this->validateInput($rules, $field, $value)) {
            $this->error($message);

            return $this->askValid($question, $field, $rules, $locale);
        }

        return $value;
    }

    protected function validateInput(array $rules, string $fieldName, string $value = null): ?string
    {
        $validator = Validator::make([
            $fieldName => $value
        ], [
            $fieldName => $rules
        ]);

        return $validator->fails() ? $validator->errors()->first($fieldName) : null;
    }
}
