<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests voor de registratie info pagina (/register/info)
 *
 * Controleert of de vereiste informatie over voedselveiligheid,
 * allergeneninformatie en wettelijke verplichtingen correct wordt getoond.
 */
class RegisterInfoPageTest extends TestCase
{
    #[Test]
    public function register_info_page_displays_food_safety_information(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check dat de introductietekst aanwezig is
        $response->assertSee('Door een account aan te maken op');
        $response->assertSee('DeBurenKoken.nl');
        $response->assertSee('ga je akkoord met het volgende:');
    }

    #[Test]
    public function register_info_page_displays_haccp_requirement(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check HACCP vereiste
        $response->assertSee('hygiënisch');
        $response->assertSee('voedselveiligheidsrichtlijnen');
        $response->assertSee('HACCP');
    }

    #[Test]
    public function register_info_page_displays_allergen_requirement(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check allergeneninformatie vereiste
        $response->assertSee('allergeneninformatie');
    }

    #[Test]
    public function register_info_page_displays_kvk_nvwa_information(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check KvK en NVWA informatie
        $response->assertSee('structurele of commerciële verkoop');
        $response->assertSee('KvK-inschrijving');
        $response->assertSee('Voedsel- en Warenautoriteit');
        $response->assertSee('NVWA');
    }

    #[Test]
    public function register_info_page_contains_haccp_link(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check link naar facts/cook pagina
        $response->assertSee(route('cook.facts'));
    }

    #[Test]
    public function register_info_page_contains_terms_checkbox_with_agreement_text(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check checkbox tekst
        $response->assertSee('Ik ga akkoord met bovenstaande en de');
        $response->assertSee('algemene voorwaarden');

        // Check link naar algemene voorwaarden
        $response->assertSee(route('terms.conditions'));
    }

    #[Test]
    public function register_info_page_has_terms_checkbox(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);

        // Check dat de checkbox aanwezig is met de juiste attributen
        $response->assertSee('name="terms"', false);
        $response->assertSee('type="checkbox"', false);
    }
}
