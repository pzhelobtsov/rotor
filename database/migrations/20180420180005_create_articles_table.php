<?php

declare(strict_types=1);

use App\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

final class CreateArticlesTable extends Migration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        if (! $this->schema->hasTable('articles')) {
            $this->schema->create('articles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id');
                $table->integer('user_id');
                $table->string('title', 50);
                $table->text('text');
                $table->string('tags', 100);
                $table->integer('rating')->default(0);
                $table->integer('visits')->default(0);
                $table->integer('count_comments')->default(0);
                $table->integer('created_at');

                $table->index('user_id');
                $table->index('created_at');
            });
        }
    }

    /**
     * Migrate Down.
     */
    public function down(): void
    {
        $this->schema->dropIfExists('articles');
    }
}
