<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::create([
            'name' => 'customer',
        ]);

        Role::create([
            'name' => 'cook',
        ]);

        Role::create([
            'name' => 'admin',
        ]);
    }

    public function down(): void
    {
        Role::where('name', '=', 'customer')->first()?->delete();
        Role::where('name', '=', 'cook')->first()?->delete();
        Role::where('name', '=', 'admin')->first()?->delete();
    }
};
