<?php

namespace App\Services;

use App\Mail\Dac7RequiredMail;
use App\Mail\Dac7WarningMail;
use App\Models\Dac7Information;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Dac7Service
{
    public function getDac7Status(User $user): array
    {
        $soldOrdersCount = $user->orders()
            ->whereNotIn('status', [Order::STATUS_GEANNULEERD])
            ->whereIn('payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
            ->count();

        // GEFIXED: Laad dish relatie EN gebruik with() in plaats van get()
        $soldOrdersRevenue = $user->orders()
            ->whereNotIn('status', [Order::STATUS_GEANNULEERD])
            ->whereIn('payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
            ->with('advert.dish') // TOEGEVOEGD: laad dish relatie
            ->get()
            ->sum(function ($order) {
                return $order->portion_amount * $order->advert->dish->portion_price;
            });

        $dac7Exceeded = $soldOrdersCount >= 20 || $soldOrdersRevenue >= 2000;

        $dac7InfoProvided = $user->dac7Information ? $user->dac7Information->information_provided : false;

        return [
            'order_count' => $soldOrdersCount,
            'total_revenue' => $soldOrdersRevenue,
            'dac7_exceeded' => $dac7Exceeded,
            'dac7_information_provided' => $dac7InfoProvided,
        ];
    }

    public function getDac7StatusExpiredOnly(User $user): array
    {
        $soldOrdersCount = $user->orders()
            ->whereNotIn('status', [Order::STATUS_GEANNULEERD])
            ->whereIn('payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
            ->whereHas('advert', function ($query) {
                $query->where('order_to', '<', now());
            })
            ->count();

        // GEFIXED: Laad dish relatie
        $soldOrdersRevenue = $user->orders()
            ->whereNotIn('status', [Order::STATUS_GEANNULEERD])
            ->whereIn('payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
            ->whereHas('advert', function ($query) {
                $query->where('order_to', '<', now());
            })
            ->with('advert.dish') // TOEGEVOEGD: laad dish relatie
            ->get()
            ->sum(function ($order) {
                return $order->portion_amount * $order->advert->dish->portion_price;
            });

        $dac7Exceeded = $soldOrdersCount >= 20 || $soldOrdersRevenue >= 2000;

        $dac7InfoProvided = $user->dac7Information ? $user->dac7Information->information_provided : false;

        return [
            'order_count' => $soldOrdersCount,
            'total_revenue' => $soldOrdersRevenue,
            'dac7_exceeded' => $dac7Exceeded,
            'dac7_information_provided' => $dac7InfoProvided,
        ];
    }

    public function calculateDac7StatusByUuid(string $userUuid): array
    {
        $soldOrdersCount = Order::where('user_uuid', $userUuid)
            ->whereNotIn('status', [Order::STATUS_GEANNULEERD])
            ->whereIn('payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
            ->count();

        // GEFIXED: Nu via dishes.portion_price met JOIN naar dishes tabel
        $soldOrdersRevenue = Order::where('orders.user_uuid', $userUuid)
            ->whereNotIn('orders.status', [Order::STATUS_GEANNULEERD])
            ->whereIn('orders.payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
            ->join('adverts', 'orders.advert_uuid', '=', 'adverts.uuid')
            ->join('dishes', 'adverts.dish_uuid', '=', 'dishes.uuid')
            ->select(DB::raw('SUM(orders.portion_amount * dishes.portion_price) as total_revenue'))
            ->value('total_revenue') ?? 0;

        $dac7Exceeded = $soldOrdersCount >= 20 || $soldOrdersRevenue >= 2000;

        $dac7InfoProvided = DB::table('dac7_informations')
            ->where('user_id', $userUuid)
            ->value('information_provided') ?? false;

        return [
            'order_count' => $soldOrdersCount,
            'total_revenue' => $soldOrdersRevenue,
            'dac7_exceeded' => $dac7Exceeded,
            'dac7_information_provided' => $dac7InfoProvided,
        ];
    }

    public function calculateUserDac7Status(string $userUuid): array
    {
        $user = User::with(['orders.advert.dish', 'dac7Information'])
            ->find($userUuid);

        if (! $user) {
            return [
                'order_count' => 0,
                'total_revenue' => 0,
                'dac7_exceeded' => false,
                'dac7_information_provided' => false,
            ];
        }

        return $this->getDac7Status($user);
    }

    public function checkUserDac7Thresholds(User $user)
    {
        if (! $user->hasRole('cook')) {
            return;
        }

        $dac7Status = $this->getDac7Status($user);

        $this->updateDac7ThresholdDate($user, $dac7Status['dac7_exceeded']);

        $dac7WarningThreshold = $dac7Status['order_count'] >= 15 || $dac7Status['total_revenue'] >= 1500;

        $warningEmailSent = $user->dac7_warning_email_sent ?? false;
        $requiredEmailSent = $user->dac7_required_email_sent ?? false;

        if ($dac7WarningThreshold && ! $warningEmailSent && $user->email) {
            $link = $this->generateDac7Link($user);
            Mail::to($user->email)
                ->cc(config('mail.admin.address'))
                ->send(new Dac7WarningMail($user));
            $this->saveDac7Link($user, $link);
            $user->update(['dac7_warning_email_sent' => true]);
        }

        if ($dac7Status['dac7_exceeded'] && ! $requiredEmailSent && $user->email) {
            $link = $this->generateDac7Link($user);
            Mail::to($user->email)
                ->cc(config('mail.admin.address'))
                ->send(new Dac7RequiredMail($user));
            $this->saveDac7Link($user, $link);
            $user->update(['dac7_required_email_sent' => true]);
        }
    }

    public function checkUserDac7ThresholdsExpiredOnly(User $user)
    {
        if (! $user->hasRole('cook')) {
            return;
        }

        $dac7Status = $this->getDac7StatusExpiredOnly($user);

        $this->updateDac7ThresholdDate($user, $dac7Status['dac7_exceeded']);

        $dac7WarningThreshold = $dac7Status['order_count'] >= 15 || $dac7Status['total_revenue'] >= 1500;

        $warningEmailSent = $user->dac7_warning_email_sent ?? false;
        $requiredEmailSent = $user->dac7_required_email_sent ?? false;

        if ($dac7WarningThreshold && ! $warningEmailSent && $user->email) {
            $link = $this->generateDac7Link($user);
            Mail::to($user->email)
                ->cc(config('mail.admin.address'))
                ->send(new Dac7WarningMail($user));
            $this->saveDac7Link($user, $link);
            $user->update(['dac7_warning_email_sent' => true]);
        }

        if ($dac7Status['dac7_exceeded'] && ! $requiredEmailSent && $user->email) {
            $link = $this->generateDac7Link($user);
            Mail::to($user->email)
                ->cc(config('mail.admin.address'))
                ->send(new Dac7RequiredMail($user));
            $this->saveDac7Link($user, $link);
            $user->update(['dac7_required_email_sent' => true]);
        }
    }

    public function updateDac7ThresholdDate(User $user, bool $dac7Exceeded)
    {
        if (! $dac7Exceeded) {
            return;
        }

        $dac7Info = Dac7Information::firstOrCreate(['user_id' => $user->uuid]);

        if (! $dac7Info->dac7_threshold_reached_at) {
            $dac7Info->update(['dac7_threshold_reached_at' => now()]);
        }
    }

    public function generateDac7Link(User $user): string
    {
        $token = hash('sha256', $user->email.$user->uuid);

        return route('dac7.form', ['uuid' => $user->uuid, 'token' => $token]);
    }

    public function saveDac7Link(User $user, string $link)
    {
        $dac7Info = Dac7Information::firstOrCreate(['user_id' => $user->uuid]);
        $dac7Info->update(['dac7_form_link' => $link]);
    }

    public function resetDac7Information(User $user)
    {
        $dac7Info = Dac7Information::where('user_id', $user->uuid)->first();

        if ($dac7Info) {
            $dac7Info->update([
                'information_provided' => false,
                'dac7_form_link' => null,
            ]);
        }

        $user->update([
            'dac7_warning_email_sent' => false,
            'dac7_required_email_sent' => false,
        ]);
    }
}
