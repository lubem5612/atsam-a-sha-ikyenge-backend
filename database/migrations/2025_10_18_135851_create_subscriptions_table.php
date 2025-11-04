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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference', 100)->index();
            $table->string('status', 20)->default('inactive')->index()->comment('active,inactive');
            $table->float('amount', 2);
            $table->string('merchant', 60)->default('paystack')->index();
            $table->string('code', 14)->index();
            $table->unsignedBigInteger('number_used' )->default(0);
            $table->string('device_id',  300)->nullable()->index();
            $table->json('details')->nullable();
            $table->timestamp('activated_on')->nullable();
            $table->timestamp('expired_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
