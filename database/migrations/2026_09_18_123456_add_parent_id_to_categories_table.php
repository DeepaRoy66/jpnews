<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // subcategory ले yo bata aafno parent category ID rakhcha — top-level category ko
            // lagi null nai basyo. self-reference भएकोले nullOnDelete: parent delete भए
            // subcategory haru orphan (parent_id = null) bancha, cascade delete hudaina.
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id') // ⚠️ tero `id` column pachi rakhna chaheko — nachaheko bhaye hataidinu
                ->constrained('categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};