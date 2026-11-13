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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Core Details
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();

            // Project Specifications (Sidebar Facts)
            $table->string('client_name')->nullable();
            $table->string('location')->nullable();
            $table->string('crop')->nullable();
            $table->string('project_type')->nullable(); // e.g. Polyhouse, Nethouse, Hydroponics
            $table->string('area')->nullable();         // e.g. 4,000 sq. m.
            $table->string('project_status')->default('Completed');
            $table->date('completed_at')->nullable();

            // Media & Gallery
            $table->string('featured_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->json('gallery_images')->nullable();

            // Custom Contact (Optional)
            $table->string('contact_phone')->nullable();
            $table->string('contact_whatsapp')->nullable();

            // SEO Metadata
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Status & Ordering
            $table->boolean('is_featured')->default(false);
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

