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
        // 1. Add avatar to users if not exists
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('password');
            });
        }

        // 2. User Profiles
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status_role')->default('student'); // student, fresh_grad, young_worker
            $table->date('birth_date')->nullable();
            $table->boolean('participate_pulse')->default(true);
            $table->json('settings_json')->nullable();
            $table->timestamps();
        });

        // 3. Daily Check-ins
        Schema::create('daily_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('mind_score', 5, 2)->default(0);
            $table->decimal('body_score', 5, 2)->default(0);
            $table->decimal('social_score', 5, 2)->default(0);
            $table->decimal('life_score', 5, 2)->default(0);
            $table->decimal('overall_wellbeing_score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('primary_tag')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });

        // 4. Detailed Life Signals (Sub-metrics)
        Schema::create('life_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkin_id')->constrained('daily_checkins')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Body Vector
            $table->decimal('sleep_hours', 4, 1)->default(7.0);
            $table->unsignedTinyInteger('sleep_quality')->default(70); // 0-100
            $table->unsignedSmallInteger('physical_activity_min')->default(15);
            $table->unsignedTinyInteger('energy_level')->default(70); // 0-100
            
            // Mind Vector
            $table->unsignedTinyInteger('stress_level')->default(30); // 0-100 (higher = more stress)
            $table->unsignedTinyInteger('focus_level')->default(70); // 0-100
            $table->unsignedTinyInteger('overthinking_level')->default(30); // 0-100
            $table->unsignedTinyInteger('mood_level')->default(70); // 0-100
            
            // Social Vector
            $table->unsignedTinyInteger('social_interaction_score')->default(70); // 0-100
            $table->unsignedTinyInteger('loneliness_score')->default(20); // 0-100
            $table->unsignedTinyInteger('relationship_friction_score')->default(10); // 0-100
            
            // Life Vector
            $table->unsignedTinyInteger('workload_score')->default(40); // 0-100
            $table->unsignedTinyInteger('financial_pressure_score')->default(30); // 0-100
            $table->unsignedTinyInteger('goal_progress_score')->default(60); // 0-100
            
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        // 5. Life Events (Deadlines, exams, milestones)
        Schema::create('life_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('work'); // exam, deadline, work, relationship, financial, health
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('severity_impact')->default(3); // 1-5
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_date']);
        });

        // 6. Recovery Activities Catalog
        Schema::create('recovery_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('sparkles');
            $table->string('category')->default('physical'); // physical, mental, social, sensory, creative
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('default_duration_min')->default(15);
            $table->timestamps();
        });

        // 7. Recovery Sessions Tracker
        Schema::create('recovery_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('recovery_activities')->cascadeOnDelete();
            $table->unsignedTinyInteger('energy_before')->default(50);
            $table->unsignedTinyInteger('energy_after')->default(70);
            $table->unsignedTinyInteger('mood_before')->default(50);
            $table->unsignedTinyInteger('mood_after')->default(75);
            $table->unsignedSmallInteger('duration_minutes')->default(15);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 8. Insights & Patterns
        Schema::create('insights_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pattern_type'); // event_correlation, sleep_energy_lag, workload_stress, social_recharge
            $table->string('title');
            $table->json('description_json');
            $table->timestamp('detected_at')->useCurrent();
            $table->string('status')->default('active'); // active, resolved, dismissed
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 9. What-If Scenarios
        Schema::create('what_if_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('target_metric'); // energy, stress, mood, focus
            $table->string('variable_change'); // sleep_plus_1h, exercise_30m, lower_workload, social_boost
            $table->decimal('potential_delta', 5, 2);
            $table->decimal('baseline_value', 5, 2)->nullable();
            $table->decimal('projected_value', 5, 2)->nullable();
            $table->json('scenario_data_json')->nullable();
            $table->timestamps();
        });

        // 10. Support Circles
        Schema::create('support_circles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('circle_name')->default('Lingkaran Utama');
            $table->timestamps();
        });

        // 11. Support Circle Members
        Schema::create('support_circle_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('support_circles')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('relationship_type')->default('sahabat'); // sahabat, keluarga, pasangan, mentor, rekan
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_pinged_at')->nullable();
            $table->timestamps();
        });

        // 12. Support Circle Pings History
        Schema::create('support_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('support_type')->default('general'); // general, vent, hangout, quiet_presence
            $table->text('custom_short_note')->nullable(); // strictly safe & voluntary
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamps();
        });

        // 13. Pulse Aggregates (Anonymous Community Trends)
        Schema::create('pulse_aggregates', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('week_number');
            $table->unsignedSmallInteger('year');
            $table->string('role_filter')->default('all'); // all, student, fresh_grad, young_worker
            $table->string('metric_name');
            $table->decimal('aggregate_value', 5, 2);
            $table->unsignedInteger('sample_count')->default(1);
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['year', 'week_number', 'role_filter']);
        });

        // 14. Knowledge Base Rules (Deterministic Rules & Micro-Actions)
        Schema::create('knowledge_base_rules', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('mind'); // mind, body, social, life, multi
            $table->json('trigger_conditions_json'); // e.g. {"body_max": 40, "sleep_max": 5.5}
            $table->string('title')->nullable();
            $table->text('reflection_prompt');
            $table->text('guided_question');
            $table->string('action_title')->nullable();
            $table->text('action_suggestion')->nullable();
            $table->string('action_suggestion_id')->nullable();
            $table->unsignedTinyInteger('priority')->default(1);
            $table->timestamps();
        });

        // 15. Reflection Journals (User's responses to reflection prompts)
        Schema::create('reflection_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('knowledge_base_rules')->nullOnDelete();
            $table->string('prompt_topic')->nullable();
            $table->text('prompt_snapshot')->nullable();
            $table->text('question_snapshot')->nullable();
            $table->text('user_response');
            $table->unsignedTinyInteger('mood_after')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 16. Privacy Logs
        Schema::create('privacy_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action_type'); // export_json, export_csv, delete_checkin, toggle_pulse, wipe_account
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // 17. Micro-Action Completed Logs (One Small Thing tracker)
        Schema::create('micro_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('action_title');
            $table->string('category'); // mind, body, social, life
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_action_logs');
        Schema::dropIfExists('privacy_logs');
        Schema::dropIfExists('reflection_journals');
        Schema::dropIfExists('knowledge_base_rules');
        Schema::dropIfExists('pulse_aggregates');
        Schema::dropIfExists('support_pings');
        Schema::dropIfExists('support_circle_members');
        Schema::dropIfExists('support_circles');
        Schema::dropIfExists('what_if_scenarios');
        Schema::dropIfExists('insights_patterns');
        Schema::dropIfExists('recovery_sessions');
        Schema::dropIfExists('recovery_activities');
        Schema::dropIfExists('life_events');
        Schema::dropIfExists('life_signals');
        Schema::dropIfExists('daily_checkins');
        Schema::dropIfExists('profiles');

        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
    }
};
