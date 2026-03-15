<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class PastTime implements Rule
{
    private Request $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function passes($attribute, $value): bool
    {
        $orderTime = Carbon::parse($this->request->input('order_date').' '.$this->request->input('order_time'), config('app.timezone'));
        $pickupFrom = Carbon::parse($this->request->input('pickup_date').' '.$this->request->input('pickup_from'), config('app.timezone'));

        return $orderTime->isAfter(Carbon::now()) && $pickupFrom->isAfter(Carbon::now());
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('validation.times-future');
    }
}
