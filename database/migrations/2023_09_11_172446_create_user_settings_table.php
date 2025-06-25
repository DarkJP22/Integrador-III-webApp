<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('option');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        $settings = \App\Setting::all();
        $settings->each(function (Setting $setting) {
            $user = \App\User::find($setting->user_id);
            if ($user) {
                \App\UserSetting::create([
                    'user_id' => $setting->user_id,
                    'option' => 'slotDuration',
                    'value' => $setting->slotDuration,
                ]);
                \App\UserSetting::create([
                    'user_id' => $setting->user_id,
                    'option' => 'minTime',
                    'value' => $setting->minTime,
                ]);
                \App\UserSetting::create([
                    'user_id' => $setting->user_id,
                    'option' => 'maxTime',
                    'value' => $setting->maxTime,
                ]);

                \App\UserSetting::create([
                    'user_id' => $setting->user_id,
                    'option' => 'freeDays',
                    'value' => $setting->freeDays,
                ]);
                \App\UserSetting::create([
                    'user_id' => $setting->user_id,
                    'option' => 'general_cost_appointment',
                    'value' => $setting->general_cost_appointment,
                ]);

            }

        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_user_id_index');
            $table->dropColumn(['user_id', 'slotDuration', 'minTime', 'maxTime', 'trial', 'trial_days', 'freeDays', 'general_cost_appointment']);
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->string('option');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('settings')->truncate();

        $config = \App\Configuration::first();

        if ($config) {
            Setting::setSetting('amount_attended', $config->amount_attended);
            Setting::setSetting('amount_expedient', $config->amount_expedient);
            Setting::setSetting('call_center', $config->call_center);
            Setting::setSetting('url_app_pacientes_android', $config->url_app_pacientes_android);
            Setting::setSetting('url_app_pacientes_ios', $config->url_app_pacientes_ios);
            Setting::setSetting('porc_accumulated', $config->porc_accumulated);
            Setting::setSetting('porc_discount_accumulated', $config->porc_discount_accumulated);
            Setting::setSetting('porc_commission', $config->porc_commission);
            Setting::setSetting('porc_reference_commission', $config->porc_reference_commission);
            Setting::setSetting('fixed_commission_general', $config->fixed_commission_general);
            Setting::setSetting('fixed_commission_specialist', $config->fixed_commission_specialist);

           // Schema::dropIfExists('configurations');
        }

        Setting::setSetting('subscriptioninvoice_number_format',  '{{SERIES:INV}}{{DELIMITER:-}}{{SEQUENCE:6}}');
        Setting::setSetting('subscription_months_free',  '1');
        Setting::setSetting('upload_max_filesize_in_mb',  '5');
        Setting::setSetting('limit_upload_files',  '10');
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
