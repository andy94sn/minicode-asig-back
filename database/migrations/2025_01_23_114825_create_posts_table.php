<?php

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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->string('name');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('status', 128)->default('draft');
            $table->integer('order')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->longText('tags')->nullable();
            $table->timestamps();

            // $table->foreignId('category_id')->constrained('categories')->onDelete('cascade')->nullOnDelete();
           $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
