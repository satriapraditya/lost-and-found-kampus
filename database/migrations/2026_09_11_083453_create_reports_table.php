<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('restrict');

            $table->foreignId('location_id')
                ->constrained('locations')
                ->onDelete('restrict');

            $table->string('type');
            $table->string('item_name');
            $table->text('description');
            $table->text('special_features')->nullable();

            $table->date('event_date');
            $table->time('event_time')->nullable();

            $table->string('status')->default('pending');

            $table->text('rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
