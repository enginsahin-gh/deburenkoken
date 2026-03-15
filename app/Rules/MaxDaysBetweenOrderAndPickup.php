<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class MaxDaysBetweenOrderAndPickup implements Rule
{
    public function passes($attribute, $value)
    {
        $request = request();

        $orderDate = $value; // Dit is de order_date
        $pickupDate = $request->input('pickup_date');
        $orderTime = $request->input('order_time');
        $pickupFrom = $request->input('pickup_from');

        if (! $orderDate || ! $pickupDate || ! $orderTime || ! $pickupFrom) {
            return true; // Laat andere validaties deze controleren
        }

        $orderDateTime = Carbon::parse($orderDate.' '.$orderTime);
        $pickupDateTime = Carbon::parse($pickupDate.' '.$pickupFrom);

        // TOEGEVOEGD: Sta dezelfde dag toe als pickup tijd na order tijd is
        if ($orderDate === $pickupDate) {
            return $pickupFrom > $orderTime;
        }

        // Voor verschillende dagen: maximaal 7 dagen verschil
        $daysDifference = $orderDateTime->diffInDays($pickupDateTime);

        return $daysDifference <= 7 && $pickupDateTime->isAfter($orderDateTime);
    }

    public function message()
    {
        return 'Afhaalmoment moet binnen 7 dagen na het uiterste bestelmoment zijn. ';
    }
}
