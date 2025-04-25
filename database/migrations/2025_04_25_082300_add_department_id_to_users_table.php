<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('role');
            $table->date('joining_date')->nullable()->after('phone');
            $table->string('profile_photo')->nullable()->after('joining_date');
            $table->foreignId('department_id')->after('profile_photo')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('joining_date');
            $table->dropColumn('profile_photo');
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
