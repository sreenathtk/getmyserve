<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','service_provider','customer','staff') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        DB::statement("DELETE FROM users WHERE role = 'staff'");
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','service_provider','customer') NOT NULL DEFAULT 'customer'");
    }
};
