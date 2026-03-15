<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class MaxThreeMonths implements Rule
{
    protected $attributeName;

    public function passes($attribute, $value)
    {
        // Bewaar de naam van het attribuut voor de foutmelding
        $this->attributeName = $attribute;

        // Controleer of de opgegeven datum geldig is
        if (is_null($value)) {
            return false; // Datum moet verplicht zijn
        }

        // Converteer de opgegeven datum naar een Carbon-object
        $date = Carbon::createFromFormat('Y-m-d', $value);

        // Drie maanden vanaf vandaag
        $threeMonthsAhead = Carbon::now()->addMonths(3);

        // Controleer of de opgegeven datum binnen drie maanden in de toekomst ligt
        return $date->lte($threeMonthsAhead);
    }

    public function message()
    {
        // Specifieke foutmelding voor het attribuut
        if ($this->attributeName === 'pickup_date') {
            return 'De ophaaldatum mag niet verder dan drie maanden in de toekomst liggen.';
        } elseif ($this->attributeName === 'order_date') {
            return 'De bestelldatum mag niet verder dan drie maanden in de toekomst liggen.';
        }

        return 'De datum mag niet verder dan drie maanden in de toekomst liggen.';
    }
}
