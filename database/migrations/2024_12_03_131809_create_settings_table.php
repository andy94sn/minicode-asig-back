<?php

use App\Enums\GroupType;
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
        Schema::create('settings', function (Blueprint $table){
            $table->id();
            $table->string('token');
            $table->string('key')->unique();
            $table->enum('group', GroupType::values())->default(GroupType::default());
            $table->json('values');
            $table->string('description')->nullable();
            $table->boolean('serialized')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
