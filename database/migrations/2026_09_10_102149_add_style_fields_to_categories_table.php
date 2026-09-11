<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'layout_type')) {
                $table->string('layout_type', 20)->default('list')->after('slug');
            }
            if (!Schema::hasColumn('categories', 'accent_color')) {
                $table->string('accent_color', 7)->default('#e30613')->after('layout_type');
            }
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon', 50)->default('bi-newspaper')->after('accent_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['layout_type', 'accent_color', 'icon']);
        });
    }
};