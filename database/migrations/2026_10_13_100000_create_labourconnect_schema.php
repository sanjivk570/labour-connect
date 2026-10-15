<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 16)->unique('users_mobile_unique');
            $table->string('role', 20);
            $table->string('preferred_locale', 5)->default('en');
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['role', 'status'], 'users_role_status_index');
        });

        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique('customer_details_user_unique')->constrained('users')->cascadeOnDelete();
            $table->string('full_name', 120)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('labour_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique('labour_categories_slug_unique');
            $table->string('name_en', 100);
            $table->string('name_hi', 100);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'sort_order'], 'labour_categories_active_order_index');
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labour_category_id')->constrained('labour_categories')->restrictOnDelete();
            $table->string('slug', 120)->unique('skills_slug_unique');
            $table->string('name_en', 100);
            $table->string('name_hi', 100);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['labour_category_id', 'is_active', 'sort_order'], 'skills_category_active_order_index');
        });

        Schema::create('labour_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique('labour_details_user_unique')->constrained('users')->cascadeOnDelete();
            $table->foreignId('labour_category_id')->nullable()->constrained('labour_categories')->nullOnDelete();
            $table->string('full_name', 120)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->string('address_line', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('max_travel_distance_km')->nullable();
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->string('work_type', 20)->nullable();
            $table->text('bio')->nullable();
            $table->unsignedTinyInteger('registration_step')->default(1);
            $table->boolean('is_available_today')->default(true);
            $table->timestamp('profile_completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['state', 'city', 'pincode'], 'labour_details_location_index');
            $table->index(['is_available_today', 'profile_completed_at'], 'labour_details_search_status_index');
        });
        Schema::create('labour_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labour_detail_id')->constrained('labour_details')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->restrictOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['labour_detail_id', 'skill_id'], 'labour_skills_pair_unique');
            $table->index('skill_id', 'labour_skills_skill_index');
        });

        Schema::create('labour_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labour_detail_id')->constrained('labour_details')->cascadeOnDelete();
            $table->string('rate_unit', 20);
            $table->decimal('amount', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['labour_detail_id', 'rate_unit'], 'labour_rates_labour_unit_unique');
            $table->index(['is_active', 'amount'], 'labour_rates_active_amount_index');
        });

        Schema::create('labour_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labour_detail_id')->constrained('labour_details')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_available')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
            $table->unique(['labour_detail_id', 'day_of_week'], 'labour_availabilities_labour_day_unique');
        });

        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('mobile', 16);
            $table->string('otp_hash', 255);
            $table->string('purpose', 30)->default('login');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();
            $table->index(['mobile', 'purpose', 'created_at'], 'otp_mobile_purpose_created_index');
            $table->index('expires_at', 'otp_expiry_index');
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token', 191)->unique('device_tokens_token_unique');
            $table->string('platform', 20);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->index('user_id', 'device_tokens_user_index');
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'customer', 'labour'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_locale_check CHECK (preferred_locale IN ('en', 'hi'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'pending', 'blocked'))");
        DB::statement('ALTER TABLE labour_details ADD CONSTRAINT labour_details_step_check CHECK (registration_step BETWEEN 1 AND 6)');
        DB::statement("ALTER TABLE labour_details ADD CONSTRAINT labour_details_work_type_check CHECK (work_type IS NULL OR work_type IN ('full_time', 'part_time', 'contract'))");
        DB::statement("ALTER TABLE labour_details ADD CONSTRAINT labour_details_gender_check CHECK (gender IS NULL OR gender IN ('male', 'female', 'other', 'prefer_not_to_say'))");
        DB::statement('ALTER TABLE labour_details ADD CONSTRAINT labour_details_distance_check CHECK (max_travel_distance_km IS NULL OR max_travel_distance_km > 0)');
        DB::statement('ALTER TABLE labour_details ADD CONSTRAINT labour_details_experience_check CHECK (experience_years IS NULL OR experience_years <= 80)');
        DB::statement('ALTER TABLE labour_details ADD CONSTRAINT labour_details_latitude_check CHECK (latitude IS NULL OR latitude BETWEEN -90 AND 90)');
        DB::statement('ALTER TABLE labour_details ADD CONSTRAINT labour_details_longitude_check CHECK (longitude IS NULL OR longitude BETWEEN -180 AND 180)');
        DB::statement("ALTER TABLE labour_rates ADD CONSTRAINT labour_rates_unit_check CHECK (rate_unit IN ('hour', 'day', 'week', 'month'))");
        DB::statement('ALTER TABLE labour_rates ADD CONSTRAINT labour_rates_amount_check CHECK (amount > 0)');
        DB::statement('ALTER TABLE labour_availabilities ADD CONSTRAINT labour_availabilities_day_check CHECK (day_of_week BETWEEN 1 AND 7)');
        DB::statement('ALTER TABLE labour_availabilities ADD CONSTRAINT labour_availabilities_time_check CHECK (start_time IS NULL OR end_time IS NULL OR end_time > start_time)');
        DB::statement("ALTER TABLE otp_verifications ADD CONSTRAINT otp_verifications_purpose_check CHECK (purpose IN ('login', 'verify_mobile'))");
        DB::statement("ALTER TABLE device_tokens ADD CONSTRAINT device_tokens_platform_check CHECK (platform IN ('android', 'ios'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('labour_availabilities');
        Schema::dropIfExists('labour_rates');
        Schema::dropIfExists('labour_skills');
        Schema::dropIfExists('labour_details');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('labour_categories');
        Schema::dropIfExists('customer_details');
        Schema::dropIfExists('users');
    }
};