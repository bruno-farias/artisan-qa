<?php


namespace App\Models\Traits;


trait SetLocale
{
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        return $this;
    }
}
