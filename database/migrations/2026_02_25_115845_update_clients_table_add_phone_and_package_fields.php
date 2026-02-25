<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('bride_phone')->nullable()->after('bride_name');
            $table->string('groom_phone')->nullable()->after('groom_name');
            $table->foreignId('package_id')->nullable()->after('event_location')->constrained()->nullOnDelete();
        });

        // Migrate data from phone to bride_phone
        \DB::table('clients')->update([
            'bride_phone' => \DB::raw('phone')
        ]);

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone')->nullable();
        });

        \DB::table('clients')->update([
            'phone' => \DB::raw('bride_phone')
        ]);

        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
            $table->dropColumn(['bride_phone', 'groom_phone']);
        });
    }
};
