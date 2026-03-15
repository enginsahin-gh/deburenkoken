<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class MinTimeDifference implements Rule
{
    private $minDifference;

    public function __construct($minDifference)
    {
        $this->minDifference = $minDifference;
    }

    public function passes($attribute, $value)
    {
        $pickupFrom = request('pickup_from');
        $pickupTo = $value;

        if (! $pickupFrom || ! $pickupTo) {
            return false;
        }

        try {
            $from = Carbon::createFromFormat('H:i', $pickupFrom);
            $to = Carbon::createFromFormat('H:i', $pickupTo);

            // Controleer eerst of 'to' na 'from' komt
            if ($to->lessThanOrEqualTo($from)) {
                return false;
            }

            // Bereken het verschil in minuten (altijd positief)
            $diffInMinutes = $from->diffInMinutes($to);

            return $diffInMinutes >= $this->minDifference;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function message()
    {
        return 'Het verschil tussen de start- en eindtijd moet minimaal '.$this->minDifference.' minuten zijn.';
    }
}
