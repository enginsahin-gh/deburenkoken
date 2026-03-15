<?php

namespace App\Rules;

use App\Models\Advert;
use App\Models\Order;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class OrderAmount implements Rule
{
    private Request $request;

    private Advert $advert;

    private string $errorMessage;

    public function __construct(
        Request $request,
        Advert $advert
    ) {
        $this->request = $request;
        $this->advert = $advert;
        $this->errorMessage = 'Er zijn niet meer genoeg porties over.'; // Standaard foutmelding
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        // Bereken het totaal aantal porties
        $totalPortions = $this->advert->portion_amount;

        // Bepaal hoeveel porties er gereserveerd zijn door SUCCEED en PAYOUT_PENDING bestellingen
        $succeededPortions = $this->advert->order()
            ->where('status', Order::STATUS_ACTIEF)
            ->where(function ($query) {
                $query->where('payment_state', Order::SUCCEED)
                    ->orWhere('payment_state', Order::PAYOUT_PENDING);
            })
            ->sum('portion_amount');

        // Bepaal hoeveel porties er in proces zijn (betalingen nog bezig)
        $inProcessPortions = $this->advert->order()
            ->where('status', Order::STATUS_ACTIEF)
            ->where('payment_state', Order::IN_PROCESS)
            ->sum('portion_amount');

        // Bereken hoeveel porties er beschikbaar zijn zonder rekening te houden met IN_PROCESS
        $availableWithoutInProcess = $totalPortions - $succeededPortions;

        // Bereken hoeveel porties er beschikbaar zijn met rekening houden met IN_PROCESS
        $availableWithInProcess = $availableWithoutInProcess - $inProcessPortions;

        // Controleer of er genoeg porties zijn met rekening houden met IN_PROCESS
        if ($availableWithInProcess >= $value) {
            return true;
        }
        // Controleer of er genoeg porties zouden zijn zonder IN_PROCESS bestellingen
        elseif ($availableWithoutInProcess >= $value && $inProcessPortions > 0) {
            $this->errorMessage = 'Wegens lopende betalingen is het door jouw geselecteerde aantal porties niet beschikbaar. Bekijk over een aantal minuten nogmaals de beschikbare aantal porties.';

            return false;
        }
        // Er zijn simpelweg niet genoeg porties
        else {
            $this->errorMessage = 'Er zijn niet meer genoeg porties over.';

            return false;
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->errorMessage;
    }
}
