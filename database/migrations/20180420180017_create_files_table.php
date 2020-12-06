<?php

declare(strict_types=1);

use App\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

final class CreateFilesTable extends Migration
{
    /**
     * Migrate Up.
     */
    public function up(): void
    {
        if (! $this->schema->hasTable('files')) {
            $this->schema->create('files', function (Blueprint $table) {
                $table->increments('id');
                $table->string('relate_type', 10);
                $table->integer('relate_id');
                $table->string('hash', 100);
                $table->string('name', 60);
                $table->integer('size');
                $table->integer('user_id');
                $table->integer('created_at');

                $table->index(['relate_type', 'relate_id']);
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
        $this->schema->dropIfExists('files');
    }
}
