<?php

use App\Enums\TranslationType;
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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->enum('type', TranslationType::values())->default(TranslationType::Text->value);
            $table->json('value');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();


            $table->unique(['key', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
