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
            $table->id(); // Primary key
            $table->string('title'); // Post title
            $table->string('slug')->unique(); // URL-friendly title
            $table->text('content'); // Main body of the post
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        // Links to users table, deletes posts if user is deleted
            $table->timestamps(); // created_at and updated_at
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
