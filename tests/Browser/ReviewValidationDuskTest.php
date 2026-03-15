<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Order;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Dusk browser test voor review textarea validatie.
 *
 * Controleert:
 * - Leestekens kunnen getypt worden in de review textarea
 * - Tekenteller werkt correct bij invoer
 */
class ReviewValidationDuskTest extends DuskTestCase
{
    /**
     * Maak een order aan en navigeer naar de review pagina.
     */
    private function visitReviewPage(Browser $browser): Browser
    {
        // Create order WITHOUT a review
        $order = Order::factory()->paid()->create();
        $client = Client::factory()->create(['user_uuid' => $order->getUserUuid()]);

        // Ensure no review exists for this order yet (force delete to bypass soft deletes)
        \App\Models\Review::where('order_uuid', $order->getUuid())->forceDelete();

        $url = route('review.order', [
            'orderUuid' => $order->getUuid(),
            'clientUuid' => $client->uuid,
        ]).'?rating=4';

        // First visit home to accept cookies, preventing page reload on review page
        $browser->visit('/')
            ->dismissOverlays();

        // Now visit the review page
        return $browser->visit($url)
            ->waitFor('#reviewText', 10);
    }

    #[Test]
    public function punctuation_can_be_typed_in_review_textarea(): void
    {
        $this->browse(function (Browser $browser) {
            $this->visitReviewPage($browser);

            // Type tekst met leestekens in de textarea
            $testText = 'Lekker eten! Goed gekruid, op tijd klaar. Aanrader?';
            $browser->type('#reviewText', $testText)
                ->pause(300);

            // Controleer dat de tekst correct in de textarea staat
            $browser->assertInputValue('#reviewText', $testText);

            $browser->screenshot('BL-202-punctuation-in-review');
        });
    }

    #[Test]
    public function character_counter_updates_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $this->visitReviewPage($browser);

            // Controleer beginstatus teller
            $browser->assertSeeIn('#charCount', '0');

            // Type tekst en controleer teller
            $testText = 'Test review!';
            $browser->type('#reviewText', $testText)
                ->pause(300);

            $browser->assertSeeIn('#charCount', (string) strlen($testText));

            $browser->screenshot('BL-202-char-counter-review');
        });
    }
}
