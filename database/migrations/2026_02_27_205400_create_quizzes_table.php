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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('result_template')->nullable(); // template HTML du résultat
            $table->boolean('require_login')->default(false);
            $table->integer('times_to_take')->default(0); // 0 = illimité
            $table->boolean('show_progress')->default(true);
            $table->boolean('auto_continue')->default(false);
            $table->boolean('email_required')->default(false);
            $table->boolean('email_user')->default(false);
            $table->boolean('email_admin')->default(false);
            $table->string('admin_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
