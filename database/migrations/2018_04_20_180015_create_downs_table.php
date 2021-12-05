<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class CreateDownsTable extends Migration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        if (! Schema::hasTable('downs')) {
            Schema::create('downs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id');
                $table->string('title', 100);
                $table->text('text');
                $table->integer('user_id');
                $table->integer('count_comments')->default(0);
                $table->integer('rating')->default(0);
                $table->integer('loads')->default(0);
                $table->boolean('active')->default(false);
                $table->integer('updated_at')->nullable();
                $table->integer('created_at');

                $table->index('category_id');
                $table->index('created_at');
            });

            if (config('database.default') === 'mysql') {
                DB::statement('CREATE FULLTEXT INDEX downs_title_text_fulltext ON downs(title, text);');
            }
        }
    }

    /**
     * Migrate Down.
     */
    public function down(): void
    {
        Schema::dropIfExists('downs');
    }
}
