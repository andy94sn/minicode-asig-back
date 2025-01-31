<?php

use App\Enums\InsuranceType;
use App\Enums\StatusType;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->nullable();
            $table->string('token');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('code');
            $table->string('certificate');
            $table->string('phone')->nullable();
            $table->enum('type', InsuranceType::values());
            $table->enum('status', StatusType::values())->default(StatusType::default());
            $table->decimal('price', 10, 2);
            $table->decimal('refund', 10, 2)->default(0);
            $table->json('info')->nullable();
            $table->string('contract')->nullable();
            $table->string('policy')->nullable();
            $table->string('demand')->nullable();
            $table->string('link')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
