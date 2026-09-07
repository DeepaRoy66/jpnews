<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step A: add temporary JSON columns
        Schema::table('news', function (Blueprint $table) {
            $table->json('title_json')->nullable()->after('title');
            $table->json('excerpt_json')->nullable()->after('excerpt');
            $table->json('body_json')->nullable()->after('body');
        });

        // Step B: migrate existing Nepali-only data into the "ne" key
        DB::table('news')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('news')->where('id', $row->id)->update([
                    'title_json'   => json_encode(['ne' => $row->title]),
                    'excerpt_json' => json_encode(['ne' => $row->excerpt]),
                    'body_json'    => json_encode(['ne' => $row->body]),
                ]);
            }
        });

        // Step C: drop old columns, rename new ones
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['title', 'excerpt', 'body']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('title_json', 'title');
            $table->renameColumn('excerpt_json', 'excerpt');
            $table->renameColumn('body_json', 'body');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('title_old')->nullable()->after('title');
            $table->text('excerpt_old')->nullable()->after('excerpt');
            $table->longText('body_old')->nullable()->after('body');
        });

        DB::table('news')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $title = json_decode($row->title, true);
                $excerpt = json_decode($row->excerpt, true);
                $body = json_decode($row->body, true);

                DB::table('news')->where('id', $row->id)->update([
                    'title_old'   => $title['ne'] ?? $title['en'] ?? '',
                    'excerpt_old' => $excerpt['ne'] ?? $excerpt['en'] ?? '',
                    'body_old'    => $body['ne'] ?? $body['en'] ?? '',
                ]);
            }
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['title', 'excerpt', 'body']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('title_old', 'title');
            $table->renameColumn('excerpt_old', 'excerpt');
            $table->renameColumn('body_old', 'body');
        });
    }
};