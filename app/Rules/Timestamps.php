<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class Timestamps implements Rule
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

        return $orderTime->isBefore($pickupFrom);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.order-before-pickup');
    }
}
