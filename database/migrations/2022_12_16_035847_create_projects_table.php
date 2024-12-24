<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->index();
            $table->mediumText('description');
            $table->string('avatar')->nullable();
            $table->float('total_paid')->nullable();
            $table->float('total_requested')->nullable();
            $table->float('min_donation_fee')->nullable();
            $table->float('increment_by')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('whatsapp')->default("");
            $table->string('country');
            $table->string('city');
            $table->string('gov');
            $table->double('lat');
            $table->double('lng');
            $table->enum('status', ["active", "archived"]);
            $table->boolean('is_public')->default(true);
	        $table->boolean('in_home')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
}
