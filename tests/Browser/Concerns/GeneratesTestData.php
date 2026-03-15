<?php

namespace Tests\Browser\Concerns;

trait GeneratesTestData
{
    /**
     * Generate a unique test email using Gmail+ aliasing
     * All emails go to winfried1@gmail.com but appear unique to the system
     */
    protected function generateTestEmail(): string
    {
        $timestamp = now()->format('YmdHis');
        $random = substr(md5(uniqid()), 0, 6);

        return "winfried1+dbk{$timestamp}{$random}@gmail.com";
    }

    /**
     * Generate a unique test username
     */
    protected function generateTestUsername(): string
    {
        $timestamp = now()->format('YmdHis');

        return "testkok{$timestamp}";
    }

    /**
     * Generate a secure random password that passes all validation rules
     */
    protected function generateTestPassword(): string
    {
        // Generate a password that meets all requirements:
        // - Min 8 chars
        // - Mixed case letters
        // - Numbers
        // - Symbols
        // - Not compromised in breaches
        return 'Test'.rand(1000, 9999).'@Dbk!'.substr(md5(uniqid()), 0, 4);
    }
}
