<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Cook;
use App\Models\CookProfileDescription;
use App\Models\Image;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CookProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');
    }

    #[Test]
    public function profile_page_loads_with_save_button_and_no_edit_button(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('Opslaan');
        $response->assertDontSee('onclick="editUserContent(true)"', false);
    }

    #[Test]
    public function profile_page_textarea_is_not_disabled(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('id="profile-description"', false);
        $response->assertDontSee('id="profile-description" maxlength="1000" disabled', false);
    }

    #[Test]
    public function profile_page_username_is_readonly_with_help_text(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('readonly', false);
        $response->assertSee('aria-readonly="true"', false);
        $response->assertSee('De thuiskok naam kan niet worden gewijzigd');
        $response->assertSee('aria-describedby="username-help"', false);
    }

    #[Test]
    public function profile_page_has_save_button_element(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('id="saveButton"', false);
        $response->assertSee('<button type="button" id="saveButton"', false);
        $response->assertSee('btn-orange', false);
    }

    #[Test]
    public function profile_page_has_success_message_container(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('id="save-success"', false);
        $response->assertSee('role="status"', false);
        $response->assertSee('Opgeslagen!');
    }

    #[Test]
    public function profile_description_can_be_saved(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->post(
            route('dashboard.settings.profile.description.post'),
            [
                'profile-description' => 'Mijn nieuwe omschrijving',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cook_profile_description', [
            'user_uuid' => $user->uuid,
            'description' => 'Mijn nieuwe omschrijving',
        ]);
    }

    #[Test]
    public function profile_description_respects_max_length(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->postJson(
            route('dashboard.settings.profile.description.post'),
            [
                'profile-description' => str_repeat('a', 1001),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('profile-description');
    }

    #[Test]
    public function profile_description_can_be_empty(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->post(
            route('dashboard.settings.profile.description.post'),
            [
                'profile-description' => '',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cook_profile_description', [
            'user_uuid' => $user->uuid,
            'description' => 'none',
        ]);
    }

    #[Test]
    public function profile_image_upload_requires_image(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->post(
            route('dashboard.settings.profile.image'),
            [
                'upload_type' => 'main',
            ]
        );

        $response->assertSessionHasErrors('profileImage');
    }

    #[Test]
    public function profile_image_upload_requires_valid_upload_type(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->postJson(
            route('dashboard.settings.profile.image'),
            [
                'profileImage' => UploadedFile::fake()->image('photo.jpg'),
                'upload_type' => 'invalid',
            ]
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function profile_image_delete_requires_uuid(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->deleteJson(
            route('dashboard.settings.profile.image.delete'),
            [
                'old_uuid' => '',
            ]
        );

        $response->assertStatus(400);
    }

    #[Test]
    public function profile_image_delete_prevents_deleting_other_users_image(): void
    {
        $user = $this->createAuthenticatedCook();
        $otherUser = $this->createAuthenticatedCook();

        $image = Image::create([
            'user_uuid' => $otherUser->uuid,
            'path' => 'img/test',
            'name' => 'test.jpg',
            'description' => 'Test image',
            'type' => '',
            'type_id' => Image::PROFILE_IMAGE,
            'main_picture' => true,
        ]);

        $response = $this->actingAs($user)->deleteJson(
            route('dashboard.settings.profile.image.delete'),
            [
                'old_uuid' => $image->uuid,
            ]
        );

        $response->assertStatus(403);
    }

    #[Test]
    public function additional_images_limited_to_three(): void
    {
        $user = $this->createAuthenticatedCook();

        for ($i = 0; $i < 3; $i++) {
            Image::create([
                'user_uuid' => $user->uuid,
                'path' => 'img/test',
                'name' => "test{$i}.jpg",
                'description' => 'Additional image',
                'type' => '',
                'type_id' => Image::PROFILE_IMAGE,
                'main_picture' => false,
            ]);
        }

        $response = $this->actingAs($user)->postJson(
            route('dashboard.settings.profile.image'),
            [
                'profileImage' => UploadedFile::fake()->image('photo.jpg'),
                'upload_type' => 'additional',
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Je kunt maximaal 3 aanvullende profielafbeeldingen hebben']);
    }

    #[Test]
    public function profile_page_has_wcag_dialog_on_upload_modal(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
        $response->assertSee('aria-labelledby="uploadModalTitle"', false);
    }

    #[Test]
    public function profile_page_has_aria_live_on_char_counter(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('aria-live="polite"', false);
        $response->assertSee('aria-atomic="true"', false);
    }

    #[Test]
    public function profile_page_has_aria_labels_on_image_buttons(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee('aria-label="Profielfoto wijzigen"', false);
        $response->assertSee('aria-label="Extra foto toevoegen"', false);
    }

    #[Test]
    public function profile_page_has_alert_role_on_upload_error(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(200);
        $response->assertSee("id='uploadError'", false);
        $response->assertSee('role="alert"', false);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_profile_page(): void
    {
        $response = $this->get(route('dashboard.settings.home'));

        $response->assertRedirect();
    }

    #[Test]
    public function customer_cannot_access_cook_profile_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::CUSTOMER);

        $response = $this->actingAs($user)->get(route('dashboard.settings.home'));

        $response->assertStatus(403);
    }

    private function createAuthenticatedCook(): User
    {
        $user = User::factory()->create([
            'kvk_naam' => 'Test KVK Bedrijf',
            'btw_nummer' => 'NL123456789B01',
            'nvwa_nummer' => '12345678',
        ]);
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        UserProfile::create([
            'user_uuid' => $user->uuid,
            'firstname' => 'Test',
            'lastname' => 'Kok',
            'phone_number' => '0612345678',
            'birthday' => now()->subYears(30),
        ]);

        return $user;
    }
}
