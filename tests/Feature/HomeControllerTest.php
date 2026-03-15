<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function home_page_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    #[Test]
    public function info_page_loads_successfully(): void
    {
        $response = $this->get(route('info'));

        $response->assertStatus(200);
    }

    #[Test]
    public function contact_page_loads_successfully(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
    }

    #[Test]
    public function customer_facts_page_loads_successfully(): void
    {
        $response = $this->get(route('customer.facts'));

        $response->assertStatus(200);
    }

    #[Test]
    public function cook_facts_page_loads_successfully(): void
    {
        $response = $this->get(route('cook.facts'));

        $response->assertStatus(200);
    }

    #[Test]
    public function cook_tips_page_loads_successfully(): void
    {
        $response = $this->get(route('cook.tips'));

        $response->assertStatus(200);
    }

    #[Test]
    public function cook_tips_page_contains_practical_tips_heading(): void
    {
        $response = $this->get(route('cook.tips'));

        $response->assertStatus(200);
        $response->assertSee('Praktische tips voor Thuiskoks');
    }

    #[Test]
    public function cook_tips_page_contains_sample_tips(): void
    {
        $response = $this->get(route('cook.tips'));

        $response->assertSee('Begin eenvoudig');
        $response->assertSee('Kies een herkenbaar gerecht');
        $response->assertSee('Houd het leuk');
    }

    #[Test]
    public function cook_tips_page_shows_navigation_tabs(): void
    {
        $response = $this->get(route('cook.tips'));

        $response->assertSee('Klanten');
        $response->assertSee('Thuiskoks');
        $response->assertSee('Praktische tips');
    }

    #[Test]
    public function cook_facts_page_shows_praktische_tips_button(): void
    {
        $response = $this->get(route('cook.facts'));

        $response->assertSee('Praktische tips');
        $response->assertSee(route('cook.tips'), false);
    }

    #[Test]
    public function customer_facts_page_shows_praktische_tips_button(): void
    {
        $response = $this->get(route('customer.facts'));

        $response->assertSee('Praktische tips');
        $response->assertSee(route('cook.tips'), false);
    }

    #[Test]
    public function terms_and_conditions_page_loads_successfully(): void
    {
        $response = $this->get(route('terms.conditions'));

        $response->assertStatus(200);
    }

    #[Test]
    public function privacy_page_loads_successfully(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertStatus(200);
    }

    #[Test]
    public function cookie_page_loads_successfully(): void
    {
        $response = $this->get(route('cookie'));

        $response->assertStatus(200);
    }

    #[Test]
    public function contact_form_requires_validation(): void
    {
        $response = $this->post(route('contact.form'), []);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function contact_form_can_be_submitted(): void
    {
        $response = $this->post(route('contact.form'), [
            'naam' => 'Test User',
            'email' => 'test@example.com',
            'onderwerp' => 'Test onderwerp',
            'bericht' => 'Dit is een testbericht.',
        ]);

        // Should redirect to success page or back
        $response->assertStatus(302);
    }

    #[Test]
    public function contact_form_submits_successfully_with_valid_data(): void
    {
        $customerEmail = 'test@gmail.com';
        $customerName = 'Jan Jansen';
        $customerPhone = '0612345678';
        $customerQuestion = 'Dit is mijn testvraag over het platform.';

        $response = $this->post(route('contact.form'), [
            'name' => $customerName,
            'email' => $customerEmail,
            'phone_number' => $customerPhone,
            'question' => $customerQuestion,
        ]);

        $response->assertRedirect(route('contact.success'));
    }

    #[Test]
    public function contact_form_admin_email_displays_customer_email_in_template(): void
    {
        $customerEmail = 'klant@voorbeeld.nl';
        $customerName = 'Jan Jansen';
        $customerPhone = '0612345678';
        $customerQuestion = 'Dit is mijn testvraag.';

        // Render de admin email view met de data
        $adminData = [
            'name' => $customerName,
            'phone' => $customerPhone,
            'email' => $customerEmail,
            'msg' => $customerQuestion,
            'content' => 'Ingevulde opmerking:',
            'admin' => true,
        ];

        $view = view('emails.contact', $adminData)->render();

        // Controleer dat het emailadres van de klant in de admin email zichtbaar is
        $this->assertStringContainsString($customerEmail, $view);
        $this->assertStringContainsString('mailto:'.$customerEmail, $view);
        $this->assertStringContainsString('Met het volgende e-mailadres:', $view);
    }

    #[Test]
    public function contact_form_customer_email_does_not_display_email_field(): void
    {
        $customerEmail = 'klant@voorbeeld.nl';
        $customerName = 'Jan Jansen';
        $customerQuestion = 'Dit is mijn testvraag.';

        // Render de klant email view (zonder admin flag)
        $customerData = [
            'name' => $customerName,
            'email' => $customerEmail,
            'msg' => $customerQuestion,
            'content' => 'Ingevulde opmerking:',
        ];

        $view = view('emails.contact', $customerData)->render();

        // Controleer dat de klant email GEEN extra emailadres veld toont
        $this->assertStringNotContainsString('Met het volgende e-mailadres:', $view);
        // Maar wel de bevestigingstekst bevat
        $this->assertStringContainsString('Wij hebben je vraag ontvangen', $view);
    }

    #[Test]
    public function custom_css_does_not_contain_incorrect_accessibility_colors(): void
    {
        $cssContent = file_get_contents(public_path('css/custom.css'));

        $this->assertStringNotContainsString('#c04500', $cssContent, 'custom.css bevat nog de incorrecte kleur #c04500 (moet #f3723b zijn)');
        $this->assertStringNotContainsString('#b03030', $cssContent, 'custom.css bevat nog de incorrecte kleur #b03030 (moet #e54750 zijn)');
    }

    #[Test]
    public function custom_css_contains_correct_brand_colors(): void
    {
        $cssContent = file_get_contents(public_path('css/custom.css'));

        $this->assertStringContainsString('#f3723b', $cssContent, 'custom.css moet de correcte oranje kleur #f3723b bevatten');
        $this->assertStringContainsString('#e54750', $cssContent, 'custom.css moet de correcte rode kleur #e54750 bevatten');
    }

    #[Test]
    public function contact_form_admin_email_displays_phone_when_provided(): void
    {
        $customerPhone = '0687654321';

        $adminData = [
            'name' => 'Test Gebruiker',
            'phone' => $customerPhone,
            'email' => 'test@voorbeeld.nl',
            'msg' => 'Testvraag',
            'admin' => true,
        ];

        $view = view('emails.contact', $adminData)->render();

        $this->assertStringContainsString($customerPhone, $view);
        $this->assertStringContainsString('Met het volgende telefoonnummer:', $view);
    }
}
