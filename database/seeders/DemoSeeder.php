<?php

namespace Database\Seeders;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Cook;
use App\Models\CookProfileDescription;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Models\WalletLine;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Cook Users ──────────────────────────────────────────────
        $cooks = [
            [
                'username' => 'fatma_koken',
                'email' => 'fatma@demo.deburenkoken.nl',
                'first' => 'Fatma',
                'last' => 'Yılmaz',
                'city' => 'Rotterdam',
                'street' => 'Witte de Withstraat',
                'house' => 42,
                'postal' => '3012BR',
                'lat' => 51.9171,
                'lng' => 4.4760,
                'desc' => 'Turkse thuiskok met passie voor traditionele Anatolische gerechten.',
                'dishes' => [
                    ['title' => 'Börek met spinazie en feta', 'desc' => 'Knapperige yufka gevuld met verse spinazie en witte kaas, gebakken in de oven.', 'halal' => true, 'veg' => true, 'vegan' => false, 'spice' => 1, 'price' => 8.50],
                    ['title' => 'Karnıyarık', 'desc' => 'Gevulde aubergine met gehakt, tomaat en paprika. Een klassieker uit de Turkse keuken.', 'halal' => true, 'veg' => false, 'vegan' => false, 'spice' => 2, 'price' => 10.00],
                    ['title' => 'Mercimek çorbası', 'desc' => 'Rode linzensoep met komijn en citroen. Veganistisch en hartverwarmend.', 'halal' => true, 'veg' => true, 'vegan' => true, 'spice' => 1, 'price' => 6.00],
                ],
            ],
            [
                'username' => 'omar_chef',
                'email' => 'omar@demo.deburenkoken.nl',
                'first' => 'Omar',
                'last' => 'El Amrani',
                'city' => 'Den Haag',
                'street' => 'Hobbemastraat',
                'house' => 18,
                'postal' => '2526JH',
                'lat' => 52.0705,
                'lng' => 4.3007,
                'desc' => 'Marokkaanse smaken uit de keuken van mijn moeder.',
                'dishes' => [
                    ['title' => 'Couscous met lamsvlees', 'desc' => 'Traditionele vrijdag-couscous met malse lam, groenten en bouillon.', 'halal' => true, 'veg' => false, 'vegan' => false, 'spice' => 2, 'price' => 12.00],
                    ['title' => 'Harira soep', 'desc' => 'Dikke Marokkaanse soep met linzen, kikkererwten en tomaat.', 'halal' => true, 'veg' => true, 'vegan' => true, 'spice' => 2, 'price' => 7.00],
                ],
            ],
            [
                'username' => 'priya_kitchen',
                'email' => 'priya@demo.deburenkoken.nl',
                'first' => 'Priya',
                'last' => 'Ramdjielal',
                'city' => 'Den Haag',
                'street' => 'Vaillantlaan',
                'house' => 65,
                'postal' => '2526AB',
                'lat' => 52.0680,
                'lng' => 4.3155,
                'desc' => 'Surinaamse gerechten met liefde bereid, net als thuis.',
                'dishes' => [
                    ['title' => 'Roti met kip', 'desc' => 'Zachte roti met malse kipmasala, kousenband en aardappel.', 'halal' => true, 'veg' => false, 'vegan' => false, 'spice' => 3, 'price' => 11.00],
                    ['title' => 'Nasi goreng', 'desc' => 'Gebakken rijst met groenten, ei, ketjap en atjar.', 'halal' => false, 'veg' => false, 'vegan' => false, 'spice' => 2, 'price' => 9.50],
                    ['title' => 'Pom', 'desc' => 'Surinaamse ovenschotel met pomtajer en kip. Feestelijk!', 'halal' => false, 'veg' => false, 'vegan' => false, 'spice' => 1, 'price' => 10.50],
                ],
            ],
            [
                'username' => 'jan_stamppot',
                'email' => 'jan@demo.deburenkoken.nl',
                'first' => 'Jan',
                'last' => 'de Vries',
                'city' => 'Utrecht',
                'street' => 'Voorstraat',
                'house' => 12,
                'postal' => '3512AE',
                'lat' => 52.0907,
                'lng' => 5.1214,
                'desc' => 'Hollandse pot met een moderne twist. Eerlijk en betaalbaar.',
                'dishes' => [
                    ['title' => 'Stamppot boerenkool', 'desc' => 'Romige boerenkoolstamppot met rookworst en jus.', 'halal' => false, 'veg' => false, 'vegan' => false, 'spice' => 0, 'price' => 8.00],
                    ['title' => 'Erwtensoep', 'desc' => 'Dikke Hollandse erwtensoep met roggebrood. Winterkost!', 'halal' => false, 'veg' => false, 'vegan' => false, 'spice' => 0, 'price' => 7.50],
                ],
            ],
            [
                'username' => 'ayse_lezzet',
                'email' => 'ayse@demo.deburenkoken.nl',
                'first' => 'Ayşe',
                'last' => 'Demir',
                'city' => 'Amsterdam',
                'street' => 'Javastraat',
                'house' => 88,
                'postal' => '1094HM',
                'lat' => 52.3614,
                'lng' => 4.9282,
                'desc' => 'Lahmacun, gözleme en meer — straateten uit Istanbul.',
                'dishes' => [
                    ['title' => 'Lahmacun', 'desc' => 'Dunne Turkse pizza met gekruid gehakt, peterselie en citroen.', 'halal' => true, 'veg' => false, 'vegan' => false, 'spice' => 2, 'price' => 5.00],
                    ['title' => 'Gözleme met kaas', 'desc' => 'Gevouwen deeg met smeltende kaas en verse kruiden.', 'halal' => true, 'veg' => true, 'vegan' => false, 'spice' => 0, 'price' => 6.50],
                ],
            ],
            [
                'username' => 'sofia_vegan',
                'email' => 'sofia@demo.deburenkoken.nl',
                'first' => 'Sofia',
                'last' => 'Bakker',
                'city' => 'Amsterdam',
                'street' => 'Bilderdijkstraat',
                'house' => 33,
                'postal' => '1053KL',
                'lat' => 52.3702,
                'lng' => 4.8688,
                'desc' => '100% plantaardig, 100% smaak. Vegan comfort food.',
                'dishes' => [
                    ['title' => 'Vegan kapsalon', 'desc' => 'Friet, vegan shoarma, sla, tomaat en knoflooksaus. Guilty pleasure!', 'halal' => true, 'veg' => true, 'vegan' => true, 'spice' => 1, 'price' => 9.00],
                    ['title' => 'Linzen dal', 'desc' => 'Indiase linzencurry met kokosmelk, kurkuma en naan.', 'halal' => true, 'veg' => true, 'vegan' => true, 'spice' => 2, 'price' => 8.50],
                ],
            ],
            [
                'username' => 'mohammed_grill',
                'email' => 'mohammed@demo.deburenkoken.nl',
                'first' => 'Mohammed',
                'last' => 'Benali',
                'city' => 'Rotterdam',
                'street' => 'Kruiskade',
                'house' => 71,
                'postal' => '3012EG',
                'lat' => 51.9225,
                'lng' => 4.4730,
                'desc' => 'Gegrilde specialiteiten en tajines. Alles halal.',
                'dishes' => [
                    ['title' => 'Kip tajine', 'desc' => 'Langzaam gegaarde kip met olijven, citroen en saffraan.', 'halal' => true, 'veg' => false, 'vegan' => false, 'spice' => 2, 'price' => 11.50],
                    ['title' => 'Kefta met tomatensaus', 'desc' => 'Gekruide gehaktballetjes in pittige tomatensaus met ei.', 'halal' => true, 'veg' => false, 'vegan' => false, 'spice' => 3, 'price' => 9.50],
                ],
            ],
            [
                'username' => 'lisa_gezond',
                'email' => 'lisa@demo.deburenkoken.nl',
                'first' => 'Lisa',
                'last' => 'van den Berg',
                'city' => 'Utrecht',
                'street' => 'Nachtegaalstraat',
                'house' => 7,
                'postal' => '3581AD',
                'lat' => 52.0850,
                'lng' => 5.1150,
                'desc' => 'Gezonde maaltijden, veel groenten. Vegetarisch en biologisch.',
                'dishes' => [
                    ['title' => 'Buddha bowl', 'desc' => 'Quinoa, geroosterde zoete aardappel, avocado, hummus en tahini dressing.', 'halal' => true, 'veg' => true, 'vegan' => true, 'spice' => 0, 'price' => 10.00],
                    ['title' => 'Gevulde paprika', 'desc' => 'Paprika gevuld met rijst, feta en zongedroogde tomaat.', 'halal' => true, 'veg' => true, 'vegan' => false, 'spice' => 1, 'price' => 9.00],
                ],
            ],
        ];

        // ── Client Users (bestellers) ──────────────────────────────
        $clientData = [
            ['username' => 'mariam_client', 'email' => 'mariam@demo.deburenkoken.nl', 'first' => 'Mariam', 'last' => 'Amrani', 'phone' => '0612345678'],
            ['username' => 'pieter_client', 'email' => 'pieter@demo.deburenkoken.nl', 'first' => 'Pieter', 'last' => 'Jansen', 'phone' => '0623456789'],
            ['username' => 'sanne_client', 'email' => 'sanne@demo.deburenkoken.nl', 'first' => 'Sanne', 'last' => 'Smit', 'phone' => '0634567890'],
            ['username' => 'yusuf_client', 'email' => 'yusuf@demo.deburenkoken.nl', 'first' => 'Yusuf', 'last' => 'Özdemir', 'phone' => '0645678901'],
            ['username' => 'emma_client', 'email' => 'emma@demo.deburenkoken.nl', 'first' => 'Emma', 'last' => 'de Groot', 'phone' => '0656789012'],
            ['username' => 'rashid_client', 'email' => 'rashid@demo.deburenkoken.nl', 'first' => 'Rashid', 'last' => 'El Idrissi', 'phone' => '0667890123'],
        ];

        $clientUsers = [];
        $clientRecords = [];

        foreach ($clientData as $cd) {
            $user = User::create([
                'uuid' => Str::uuid()->toString(),
                'username' => $cd['username'],
                'email' => $cd['email'],
                'password' => Hash::make('demo1234'),
                'email_verified_at' => $now,
            ]);

            UserProfile::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'firstname' => $cd['first'],
                'lastname' => $cd['last'],
                'phone_number' => $cd['phone'],
                'birthday' => Carbon::create(1990, rand(1, 12), rand(1, 28)),
            ]);

            $client = Client::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'name' => $cd['first'] . ' ' . $cd['last'],
                'email' => $cd['email'],
                'phone_number' => $cd['phone'],
            ]);

            $clientUsers[] = $user;
            $clientRecords[] = $client;
        }

        // ── Create Cooks, Dishes, Adverts, Orders, Reviews ─────────
        $allAdverts = [];
        $cookUsers = [];

        foreach ($cooks as $cookData) {
            // User
            $user = User::create([
                'uuid' => Str::uuid()->toString(),
                'username' => $cookData['username'],
                'email' => $cookData['email'],
                'password' => Hash::make('demo1234'),
                'email_verified_at' => $now,
                'type_thuiskok' => 1,
            ]);
            $cookUsers[] = $user;

            // UserProfile
            UserProfile::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'firstname' => $cookData['first'],
                'lastname' => $cookData['last'],
                'phone_number' => '06' . rand(10000000, 99999999),
                'birthday' => Carbon::create(rand(1975, 1995), rand(1, 12), rand(1, 28)),
            ]);

            // Cook
            $cook = Cook::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'lat' => $cookData['lat'],
                'long' => $cookData['lng'],
                'street' => $cookData['street'],
                'house_number' => $cookData['house'],
                'postal_code' => $cookData['postal'],
                'city' => $cookData['city'],
                'country' => 'NL',
                'description' => $cookData['desc'],
            ]);

            // Cook profile description
            CookProfileDescription::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'description' => $cookData['desc'],
            ]);

            // Wallet
            Wallet::create([
                'uuid' => Str::uuid()->toString(),
                'user_uuid' => $user->uuid,
                'state' => Wallet::FULL,
                'total_available' => rand(20, 150) + (rand(0, 99) / 100),
                'total_processing' => rand(0, 30) + (rand(0, 99) / 100),
                'total_paid' => rand(50, 500) + (rand(0, 99) / 100),
            ]);

            // Dishes
            foreach ($cookData['dishes'] as $dishData) {
                $dish = Dish::create([
                    'uuid' => Str::uuid()->toString(),
                    'user_uuid' => $user->uuid,
                    'cook_uuid' => $cook->uuid,
                    'title' => $dishData['title'],
                    'description' => $dishData['desc'],
                    'is_halal' => $dishData['halal'],
                    'is_vegetarian' => $dishData['veg'],
                    'is_vegan' => $dishData['vegan'],
                    'has_alcohol' => false,
                    'has_gluten' => rand(0, 1),
                    'has_lactose' => rand(0, 1),
                    'spice_level' => $dishData['spice'],
                    'portion_price' => $dishData['price'],
                ]);

                // Create 1-2 future adverts per dish
                $advertCount = rand(0, 1);
                for ($a = 0; $a <= $advertCount; $a++) {
                    $pickupDate = $now->copy()->addDays(rand(1, 14));
                    $orderDate = $pickupDate->copy()->subDay();

                    $advert = Advert::create([
                        'uuid' => Str::uuid()->toString(),
                        'dish_uuid' => $dish->uuid,
                        'portion_amount' => rand(5, 15),
                        'pickup_date' => $pickupDate->toDateString(),
                        'pickup_from' => sprintf('%02d:00:00', rand(17, 18)),
                        'pickup_to' => sprintf('%02d:00:00', rand(19, 20)),
                        'order_date' => $orderDate->toDateString(),
                        'order_time' => '23:59:00',
                        'published' => $now,
                    ]);
                    $allAdverts[] = ['advert' => $advert, 'dish' => $dish, 'cook_user' => $user];
                }
            }
        }

        // ── Orders + Reviews ────────────────────────────────────────
        $reviews = [
            'Heerlijk! Smaakte net als bij mijn oma thuis.',
            'Heel lekker en goed gekruid. Zeker opnieuw bestellen!',
            'Verse ingrediënten, je proeft het verschil.',
            'Fijne porties, meer dan genoeg voor twee personen.',
            'Top kok! Altijd op tijd en alles smaakt fantastisch.',
            'De kinderen vonden het ook lekker, dat zegt genoeg.',
            'Mooie presentatie en geweldige smaken.',
            'Beetje pittig voor mij maar wel heel smaakvol.',
            'Aanrader! Lekker, betaalbaar en gezellig opgehaald.',
            'Was de eerste keer, maar zeker niet de laatste!',
            'Authentiek en met liefde gemaakt, dat proef je.',
            'Perfecte maaltijd na een drukke werkdag.',
        ];

        // Create 12+ orders spread across adverts
        $orderCount = 0;
        foreach ($allAdverts as $idx => $advertInfo) {
            $numOrders = rand(1, 3);
            for ($o = 0; $o < $numOrders && $orderCount < 15; $o++) {
                $clientIdx = $orderCount % count($clientRecords);
                $clientUser = $clientUsers[$clientIdx];
                $client = $clientRecords[$clientIdx];
                $advert = $advertInfo['advert'];
                $dish = $advertInfo['dish'];
                $cookUser = $advertInfo['cook_user'];

                $portions = rand(1, 3);
                $pickupTime = Carbon::parse($advert->pickup_date . ' ' . $advert->pickup_from)
                    ->addMinutes(rand(0, 60));

                $order = Order::create([
                    'uuid' => Str::uuid()->toString(),
                    'user_uuid' => $clientUser->uuid,
                    'dish_uuid' => $dish->uuid,
                    'client_uuid' => $client->uuid,
                    'advert_uuid' => $advert->uuid,
                    'portion_amount' => $portions,
                    'expected_pickup_time' => $pickupTime,
                    'remarks' => rand(0, 1) ? 'Graag extra saus erbij' : null,
                    'payment_state' => Order::SUCCEED,
                    'status' => Order::STATUS_ACTIEF,
                    'review_send' => $now->copy()->subDays(rand(1, 5)),
                ]);

                // Wallet line for this order
                $cookWallet = Wallet::where('user_uuid', $cookUser->uuid)->first();
                if ($cookWallet) {
                    WalletLine::create([
                        'uuid' => Str::uuid()->toString(),
                        'wallet_uuid' => $cookWallet->uuid,
                        'order_uuid' => $order->uuid,
                        'amount' => $dish->portion_price * $portions,
                        'state' => WalletLine::AVAILABLE,
                    ]);
                }

                // Review for ~70% of orders
                if (rand(1, 10) <= 7) {
                    Review::create([
                        'uuid' => Str::uuid()->toString(),
                        'user_uuid' => $cookUser->uuid,
                        'order_uuid' => $order->uuid,
                        'client_uuid' => $client->uuid,
                        'anonymous' => rand(0, 1),
                        'rating' => rand(3, 5),
                        'review' => $reviews[$orderCount % count($reviews)],
                    ]);
                }

                $orderCount++;
            }
        }

        $this->command->info("DemoSeeder: created 8 cooks, {$orderCount} orders, " . count($allAdverts) . " adverts.");
    }
}
