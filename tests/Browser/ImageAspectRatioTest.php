<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\Concerns\ProvidesTestAdverts;
use Tests\DuskTestCase;

class ImageAspectRatioTest extends DuskTestCase
{
    use ProvidesTestAdverts;

    // --- Desktop tests (1920x1080) ---

    #[Test]
    public function search_results_dish_images_fill_container_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $advertUrl = $this->findOrCreateAdvert($browser);

            if ($advertUrl === null) {
                $this->markTestSkipped('No adverts available');

                return;
            }

            $browser->visit('/search?plaats=Sliedrecht&latitude=51.8248681&longitude=4.773162399999999&distance=100&searching=1')
                ->pause(2000);

            $browser->dismissOverlays();

            $hasDishImg = $browser->script("return document.querySelector('.dish-row .dish-img') !== null")[0];

            if ($hasDishImg) {
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.dish-row .dish-img')).objectFit")[0];
                $width = $browser->script("return getComputedStyle(document.querySelector('.dish-row .dish-img')).width")[0];
                $containerWidth = $browser->script("return document.querySelector('.dish-row .dish-img').parentElement.offsetWidth")[0];
                $imageWidth = $browser->script("return document.querySelector('.dish-row .dish-img').offsetWidth")[0];

                $this->assertEquals('contain', $objectFit, 'Dish image should have object-fit: contain');
                $this->assertEquals($containerWidth, $imageWidth, 'Dish image should fill the container width');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-search-results-desktop');
        });
    }

    #[Test]
    public function cook_list_images_do_not_overflow_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search/cooks?username='.DuskTestSeeder::TEST_USERNAME)
                ->pause(2000);

            $browser->dismissOverlays();

            $hasCookImg = $browser->script("return document.querySelector('.cook-row img') !== null")[0];

            if ($hasCookImg) {
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.cook-row img')).objectFit")[0];
                $imgWidth = $browser->script("return document.querySelector('.cook-row img').offsetWidth")[0];
                $containerWidth = $browser->script("return document.querySelector('.cook-row img').parentElement.offsetWidth")[0];

                $this->assertEquals('contain', $objectFit, 'Cook list image should have object-fit: contain');
                $this->assertLessThanOrEqual($containerWidth, $imgWidth, 'Cook list image should not overflow its container');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-cook-list-desktop');
        });
    }

    #[Test]
    public function cook_profile_showcase_uses_contain_and_top_alignment_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $cookUuid = $this->findCookUuid($browser);

            if ($cookUuid === null) {
                $this->markTestSkipped('No cook profile available');

                return;
            }

            $browser->visit("/search/cooks/{$cookUuid}/details")
                ->pause(2000);

            $browser->dismissOverlays();

            $hasPsLayer = $browser->script("return document.querySelector('.ps-layer') !== null")[0];

            if ($hasPsLayer) {
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.ps-layer')).objectFit")[0];
                $objectPosition = $browser->script("return getComputedStyle(document.querySelector('.ps-layer')).objectPosition")[0];

                $this->assertEquals('contain', $objectFit, 'Product showcase large image should use object-fit: contain');
                $this->assertStringContainsString('top', $objectPosition, 'Product showcase large image should be top-aligned');
            }

            $hasPsIcon = $browser->script("return document.querySelector('.ps-icon') !== null")[0];

            if ($hasPsIcon) {
                $bgSize = $browser->script("return getComputedStyle(document.querySelector('.ps-icon')).backgroundSize")[0];
                $this->assertEquals('contain', $bgSize, 'Product showcase thumbnail should use background-size: contain');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-cook-profile-desktop');
        });
    }

    #[Test]
    public function advert_detail_image_fills_container_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $advertUrl = $this->findOrCreateAdvert($browser);

            if ($advertUrl === null) {
                $this->markTestSkipped('No adverts available');

                return;
            }

            $browser->visit($advertUrl)
                ->pause(2000);

            $browser->dismissOverlays();

            $hasSingleImg = $browser->script("return document.querySelector('.single-dish .single-img img') !== null")[0];

            if ($hasSingleImg) {
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.single-dish .single-img img')).objectFit")[0];
                $this->assertEquals('contain', $objectFit, 'Advert detail image should have object-fit: contain');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-advert-detail-desktop');
        });
    }

    #[Test]
    public function order_page_dish_image_fills_container_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $advertUrl = $this->findOrCreateAdvert($browser);

            if ($advertUrl === null) {
                $this->markTestSkipped('No adverts available');

                return;
            }

            $browser->visit($advertUrl.'/order')
                ->pause(2000);

            $browser->dismissOverlays();

            $hasDishTopImg = $browser->script("return document.querySelector('.dish-top .img-holder img') !== null")[0];

            if ($hasDishTopImg) {
                $width = $browser->script("return getComputedStyle(document.querySelector('.dish-top .img-holder img')).width")[0];
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.dish-top .img-holder img')).objectFit")[0];

                $this->assertEquals('contain', $objectFit, 'Order page dish image should have object-fit: contain');
                $this->assertNotEquals('auto', $width, 'Order page dish image width should not be auto');
            }

            // Verify badge positioning: .dish-top .col-3 must be position: relative
            // so the .nog badge (position: absolute) anchors to the image column
            $hasCol3 = $browser->script("return document.querySelector('.dish-top .col-3') !== null")[0];

            if ($hasCol3) {
                $col3Position = $browser->script("return getComputedStyle(document.querySelector('.dish-top .col-3')).position")[0];
                $this->assertEquals('relative', $col3Position, 'Order page .dish-top .col-3 should have position: relative for badge anchoring');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-order-page-desktop');
        });
    }

    #[Test]
    public function cook_profile_showcase_does_not_inject_inline_styles(): void
    {
        $this->browse(function (Browser $browser) {
            $cookUuid = $this->findCookUuid($browser);

            if ($cookUuid === null) {
                $this->markTestSkipped('No cook profile available');

                return;
            }

            $browser->visit("/search/cooks/{$cookUuid}/details")
                ->pause(2000);

            $browser->dismissOverlays();

            // The old JS injected a <style> tag with mobile styles including
            // .ps-layer { height: auto } and .ps-icon-wrapper { position: fixed }.
            // Verify these problematic inline styles are no longer present.
            $hasInlineFixed = $browser->script("
                const styles = document.querySelectorAll('style');
                for (const s of styles) {
                    if (s.textContent.includes('ps-icon-wrapper') && s.textContent.includes('position: fixed')) {
                        return true;
                    }
                }
                return false;
            ")[0];

            $this->assertFalse($hasInlineFixed, 'Product showcase should not inject inline styles with fixed positioning');
        });
    }

    // --- Mobile tests (375x812) ---

    #[Test]
    public function search_results_dish_images_scale_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $advertUrl = $this->findOrCreateAdvert($browser);

            if ($advertUrl === null) {
                $this->markTestSkipped('No adverts available');

                return;
            }

            $browser->resize(375, 812)
                ->visit('/search?plaats=Sliedrecht&latitude=51.8248681&longitude=4.773162399999999&distance=100&searching=1')
                ->pause(2000);

            $browser->dismissOverlays();

            $hasDishImg = $browser->script("return document.querySelector('.dish-row .dish-img') !== null")[0];

            if ($hasDishImg) {
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.dish-row .dish-img')).objectFit")[0];
                $containerWidth = $browser->script("return document.querySelector('.dish-row .dish-img').parentElement.offsetWidth")[0];
                $imageWidth = $browser->script("return document.querySelector('.dish-row .dish-img').offsetWidth")[0];

                $this->assertEquals('contain', $objectFit, 'Dish image should have object-fit: contain on mobile');
                $this->assertEquals($containerWidth, $imageWidth, 'Dish image should fill container width on mobile');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-search-results-mobile');
            $browser->resize(1920, 1080);
        });
    }

    #[Test]
    public function cook_list_images_do_not_overflow_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 812)
                ->visit('/search/cooks?username='.DuskTestSeeder::TEST_USERNAME)
                ->pause(2000);

            $browser->dismissOverlays();

            $hasCookImg = $browser->script("return document.querySelector('.cook-row img') !== null")[0];

            if ($hasCookImg) {
                $imgWidth = $browser->script("return document.querySelector('.cook-row img').offsetWidth")[0];
                $containerWidth = $browser->script("return document.querySelector('.cook-row img').parentElement.offsetWidth")[0];

                $this->assertLessThanOrEqual($containerWidth, $imgWidth, 'Cook list image should not overflow its container on mobile');
                $this->assertEquals(130, $imgWidth, 'Cook list image should be constrained to 130px on mobile');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-cook-list-mobile');
            $browser->resize(1920, 1080);
        });
    }

    #[Test]
    public function dashboard_dish_detail_image_uses_contain(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(\App\Models\User::where('email', \Database\Seeders\DuskTestSeeder::TEST_EMAIL)->first())
                ->visit('/dashboard/dishes')
                ->pause(1000);

            $browser->dismissOverlays();

            $dishLink = $browser->script("
                const link = document.querySelector('a[href*=\"/dashboard/dishes/show/\"]');
                return link ? link.href : null;
            ")[0];

            if ($dishLink === null) {
                $this->markTestSkipped('No dashboard dish available');

                return;
            }

            $browser->visit($dishLink)
                ->pause(1000);

            $browser->dismissOverlays();

            $hasImgHolder = $browser->script("return document.querySelector('.single-dish .img-holder img') !== null")[0];

            if ($hasImgHolder) {
                $objectFit = $browser->script("return getComputedStyle(document.querySelector('.single-dish .img-holder img')).objectFit")[0];
                $this->assertEquals('contain', $objectFit, 'Dashboard dish detail image should have object-fit: contain');
            }
        });
    }

    #[Test]
    public function cook_profile_showcase_height_is_reduced_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $cookUuid = $this->findCookUuid($browser);

            if ($cookUuid === null) {
                $this->markTestSkipped('No cook profile available');

                return;
            }

            $browser->resize(375, 812)
                ->visit("/search/cooks/{$cookUuid}/details")
                ->pause(2000);

            $browser->dismissOverlays();

            $hasPsLayer = $browser->script("return document.querySelector('.ps-layer') !== null")[0];

            if ($hasPsLayer) {
                $height = $browser->script("return parseInt(getComputedStyle(document.querySelector('.ps-layer')).height)")[0];
                $this->assertLessThanOrEqual(300, $height, 'Product showcase height should be 300px or less on mobile');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-cook-profile-mobile');
            $browser->resize(1920, 1080);
        });
    }

    #[Test]
    public function advert_detail_image_height_is_reduced_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $advertUrl = $this->findOrCreateAdvert($browser);

            if ($advertUrl === null) {
                $this->markTestSkipped('No adverts available');

                return;
            }

            $browser->resize(375, 812)
                ->visit($advertUrl)
                ->pause(2000);

            $browser->dismissOverlays();

            $hasSingleImg = $browser->script("return document.querySelector('.single-dish .single-img img') !== null")[0];

            if ($hasSingleImg) {
                $height = $browser->script("return parseInt(getComputedStyle(document.querySelector('.single-dish .single-img img')).height)")[0];
                $this->assertLessThanOrEqual(300, $height, 'Advert detail image height should be 300px or less on mobile');
            }

            $browser->screenshot('BL-233/BL-233-AFTER-advert-detail-mobile');
            $browser->resize(1920, 1080);
        });
    }

    /**
     * Find a cook UUID by searching for the Dusk test cook.
     */
    private function findCookUuid(Browser $browser): ?string
    {
        $browser->visit('/search/cooks?username='.DuskTestSeeder::TEST_USERNAME)
            ->pause(2000);

        $browser->dismissOverlays();

        $uuid = $browser->script("
            const link = document.querySelector('.cook-row a[href*=\"/search/cooks/\"]');
            if (!link) return null;
            const match = link.href.match(/\\/search\\/cooks\\/([a-f0-9-]+)\\/details/);
            return match ? match[1] : null;
        ")[0];

        return $uuid;
    }
}
