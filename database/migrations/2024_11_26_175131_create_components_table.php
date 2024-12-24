<?php

use App\Enums\ComponentType;
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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->string('token', 191);
            $table->string('title');
            $table->string('key');
            $table->enum('type', ComponentType::values())->default(ComponentType::default());
            $table->json('content');
            $table->boolean('status')->default(1);
            $table->integer('order')->default(1);
            $table->timestamps();

            $table->foreignId('section_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
