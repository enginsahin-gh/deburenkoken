<?php

namespace Tests\Browser\Concerns;

use Laravel\Dusk\Browser;

trait AuthenticatesUsers
{
    use GeneratesTestData;

    /**
     * Stored credentials for current test session
     */
    protected ?string $sessionEmail = null;

    protected ?string $sessionPassword = null;

    protected ?string $sessionUsername = null;

    /**
     * Register a new cook account, verify email, and return credentials
     * Uses Gmail+ aliasing to ensure emails go to winfried1@gmail.com
     *
     * This method handles the complete registration flow:
     * 1. Fills registration form
     * 2. Submits form
     * 3. Opens verification email from debug bar
     * 4. Clicks verification link
     * 5. User is automatically logged in after verification
     */
    protected function registerAndVerifyCook(Browser $browser): array
    {
        // Generate unique credentials
        $this->sessionUsername = $this->generateTestUsername();
        $this->sessionEmail = $this->generateTestEmail();
        $this->sessionPassword = $this->generateTestPassword();

        // Navigate to registration page and wait for form
        $browser->visit('/register/info')
            ->waitFor('input[name="username"]', 10)
            ->type('input[name="username"]', $this->sessionUsername)
            ->type('input[name="email"]', $this->sessionEmail)
            ->type('input[name="password"]', $this->sessionPassword)
            ->type('input[name="password_confirmation"]', $this->sessionPassword)
            ->check('input[name="terms"]')
            ->press('Account aanmaken')
            ->waitForLocation('/register/submitted', 10)
            ->assertSee('Account aanvragen succesvol');

        // Open verification email from debug bar
        // The debug bar shows "Mails 1" and has a "View Mail" link
        $browser->clickLink('View Mail')
            ->pause(1000); // Wait for mail popup to open

        // Extract verification URL from the email
        // The verification link is in the mail preview iframe/popup
        $verificationUrl = $browser->evaluate(
            'document.body.innerHTML.match(/href="(.*?register\\/verify.*?)"/)?.[1]'
        );

        if (! $verificationUrl) {
            throw new \RuntimeException('Could not find verification URL in email');
        }

        // Visit verification URL
        $browser->visit($verificationUrl)
            ->waitFor('h1', 10)
            ->assertSee('Account succesvol aangemaakt')
            ->assertSee($this->sessionUsername);

        // User is now automatically logged in after verification

        return [
            'username' => $this->sessionUsername,
            'email' => $this->sessionEmail,
            'password' => $this->sessionPassword,
        ];
    }

    /**
     * Log out the current user
     */
    protected function logout(Browser $browser): void
    {
        $browser->visit('/logout')
            ->waitForLocation('/', 5);
    }

    /**
     * Ensure user is logged out before test
     */
    protected function ensureLoggedOut(Browser $browser): void
    {
        $browser->visit('/logout');
    }
}
