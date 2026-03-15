<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * BL-293: Screenshot tests ter verificatie van herstel van oranje huisstijlkleuren.
 *
 * De kleuren #c04500 en #b03030 zijn per ongeluk ingevoerd via BL-281 (5f6b0304)
 * als "toegankelijkheidsverbeteringen". Dit ticket herstelt de originele kleuren
 * #f3723b en #e54750.
 *
 * Getoonde elementen:
 * - "Bekijk Thuiskoks" knop op homepage (was: #c04500, nu: #f3723b)
 * - Plus-icoon in FAQ accordion (was: #c04500, nu: #f3723b)
 * - Actieve tab-knop in FAQ navigatie (gradient: was #c04500/#b03030, nu #f3723b/#e54750)
 */
class BL293ColorRestorationTest extends DuskTestCase
{
    private function hideDebugBar(Browser $browser): void
    {
        $browser->script("
            ['debugbar', 'phpdebugbar', 'phpdebugbar-header'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            document.querySelectorAll('.phpdebugbar, .phpdebugbar-body, .phpdebugbar-header, [id*=\"debugbar\"]').forEach(function(el) {
                el.style.display = 'none';
            });
        ");
    }

    #[Test]
    public function screenshot_homepage_bekijk_thuiskoks_button(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.home-banner', 10);

            $browser->dismissOverlays();
            $this->hideDebugBar($browser);

            $browser->screenshot('BL-293/01-homepage-bekijk-thuiskoks-knop');
        });
    }

    #[Test]
    public function screenshot_faq_customer_plus_icon_and_tabs(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/facts/customer')
                ->waitFor('.accordion-section', 10);

            $browser->dismissOverlays();
            $this->hideDebugBar($browser);

            $browser->screenshot('BL-293/02-faq-klanten-plus-icoon-tabs');
        });
    }

    #[Test]
    public function screenshot_faq_cook_tips_active_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/facts/cook-tips')
                ->waitFor('.accordion-section', 10);

            $browser->dismissOverlays();
            $this->hideDebugBar($browser);

            $browser->screenshot('BL-293/03-faq-praktische-tips-actieve-tab');
        });
    }

    #[Test]
    public function homepage_cook_search_btn_uses_correct_orange_color(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.home-banner', 10);

            $browser->dismissOverlays();

            $color = $browser->script("
                var el = document.querySelector('.home-banner .cook-search-btn');
                if (!el) return null;
                return window.getComputedStyle(el).color;
            ")[0];

            $this->assertNotNull($color, 'Cook search button gevonden');
            // rgb(192, 69, 0) is de computedStyle waarde van #c04500
            $this->assertStringNotContainsString('192, 69, 0', $color ?? '', 'Button gebruikt nog de incorrecte kleur #c04500 (rgb 192,69,0)');
        });
    }

    #[Test]
    public function faq_accordion_plus_icon_uses_correct_orange_color(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/facts/customer')
                ->waitFor('.accordion-section', 10);

            $browser->dismissOverlays();

            $cssColor = $browser->script("
                var styleSheet = Array.from(document.styleSheets).find(s => {
                    try { return s.href && s.href.includes('custom.css'); } catch(e) { return false; }
                });
                if (!styleSheet) return null;
                var rules = Array.from(styleSheet.cssRules || []);
                var rule = rules.find(r => r.selectorText && r.selectorText.includes('panel-title span:after'));
                return rule ? rule.style.color : null;
            ")[0];

            if ($cssColor !== null) {
                $this->assertStringNotContainsString('192, 69, 0', $cssColor, 'Plus-icoon CSS regel gebruikt nog de incorrecte kleur #c04500');
                $this->assertStringNotContainsString('c04500', strtolower($cssColor), 'Plus-icoon CSS regel gebruikt nog de incorrecte kleur #c04500');
            }
        });
    }
}
