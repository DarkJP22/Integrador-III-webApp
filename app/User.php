<?php

namespace App;

use App\Enums\OfficeType;
use App\Enums\TypeOfHealthProfessional;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Log;
use Storage;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client as TwilioClient;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'password',
        'api_token',
        'speciality_id',
        'active',
        'phone_number',
        'phone_country_code',
        'commission',
        'medic_code',
        'ide',
        'push_token',
        'fe',
        'available_expedient',
        'available_expedient_date',
        'authorization_expedient_code',
        'accumulated_affiliation',
        'current_role_id',
        'changed_password',
        'disabled_by_payment',
        'type_of_health_professional'
    ];
    protected $appends = ['fullPhone'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'push_token',
        'authorization_expedient_code'
    ];

    protected $casts = [
        'type_of_health_professional' => TypeOfHealthProfessional::class,
        'social_profile_fields' => AsArrayObject::class,
    ];

    public static function byPhone($phone, $code, $email = null): User
    {
        //$email = $email ? $email : auth()->user()->email;

        return static::where('phone_number', $phone)
            ->where('phone_country_code', $code)
            ->when($email, fn($query) => $query->where('email', $email))
            ->firstOrFail();
    }

    public static function byEmail($email): User
    {
        //$email = $email ? $email : auth()->user()->email;

        return static::where('email', $email)->firstOrFail();
    }

    public function getFullPhoneAttribute(): string
    {
        return $this->phone_country_code . $this->phone_number;
    }


    public function getAvatarPathAttribute($avatar): string
    {
        if (!$avatar) return asset('img/default-avatar.jpg');


        return Storage::disk('s3')->url($avatar);
    }

    public function scopeSearch($query, $search): Builder
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%')
                    ->orWhere('users.phone_number', 'like', '%' . $search . '%')
                    ->orWhere('users.ide', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public function scopeActive($query, $search): Builder
    {

        return $query->where(function ($query) use ($search) {
            $query->where('active', $search ?: 1);
        });
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint): Builder
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }


    public function assignRole($role): array
    {
        return $this->roles()->sync($role);
    }


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function currentRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'current_role_id');
    }

    public function assignSpeciality($speciality): array
    {
        return $this->specialities()->sync($speciality);
    }

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class);
    }

    public function hasNotSpeciality($speciality): bool
    {
        return !$this->hasSpeciality($speciality);
    }

    public function hasSpeciality($speciality): bool
    {
        if ($speciality instanceof Speciality) {
            $speciality = $speciality->id;
        }

        if (is_string($speciality)) {
            return $this->specialities->contains('name', $speciality);
        }

        return $this->specialities->contains('id', $speciality);
    }

    public function hasPatient($patient): bool
    {
        if ($patient instanceof Patient) {
            $patient = $patient->id;
        }

        return $this->patients->contains('id', $patient);
    }

    public function monthlyCharge(): Collection
    {


        return Income::where('user_id', $this->id)->where(function ($query) {
            $query->where('type', 'M') // por cita atendida
                ->orWhere('type', 'MS'); // por subscripcion de paquete
        })->where('paid', 0)->get();
    }

    public function expiredSubscription(): Collection
    {

        return Income::where('user_id', $this->id)->where(function ($query) {
            $query->where('type', 'MS'); // por subscripcion de paquete
        })->where('paid', 0)->get();
    }


    public function hasSubscription($subscription = null): bool
    {
        if ($this->subscription && $subscription && (is_string($subscription) || is_numeric($subscription))) {
            return $this->subscription->plan_id == $subscription;
        }

        return (bool) $this->subscription;
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function subscriptionPlanHasAssistant(): bool
    {
        if (!$this->subscription) {
            return false;
        }

        $plan = Plan::find($this->subscription->plan_id);

        return $plan && $plan->include_assistant;
    }

    public function subscriptionPlanHasFe(): bool
    {
        if (!$this->subscription) {
            return false;
        }

        $plan = Plan::find($this->subscription->plan_id);

        return $plan && $plan->include_fe;
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class)->using(PatientUser::class)->withPivot('authorization', 'authorized_at', 'authorization_code');
    }




    //    public function settings()
    //    {
    //        return $this->hasOne(Setting::class);
    //    }

    //    /**
    //     * create a setting to user
    //     * @param null $profile
    //     * @return mixed
    //     */
    //    public function createSettings($setting = null)
    //    {
    //        $setting = ($setting) ? $setting : new Setting();
    //
    //        return $this->settings()->save($setting);
    //    }

    public function verifyOffice($office): bool
    {
        if ($office instanceof Office) {
            $office = $office->id;
        }

        return DB::table('office_user')
            ->where('office_id', $office)
            ->where('user_id', $this->id)
            ->where('verified', 1)->exists();
    }

    public function hasPermissionFeOffice($office): bool
    {
        if ($office instanceof Office) {
            $office = $office->id;
        }

        return DB::table('office_user')
            ->where('office_id', $office)
            ->where('user_id', $this->id)
            ->where('permission_fe', 1)->exists();
    }

    public function pharmacies(): BelongsToMany
    {
        return $this->belongsToMany(Pharmacy::class);
    }

    public function clinicsAssistants(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'assistants_offices', 'assistant_id', 'office_id');
    }

    public function pharmaciesAssistants(): BelongsToMany
    {
        return $this->belongsToMany(Pharmacy::class, 'assistants_pharmacies', 'assistant_id', 'pharmacy_id');
    }

    public function addAssistant(User $user): void
    {
        $this->assistants()->attach($user->id);
    }

    public function assistants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assistants_users', 'user_id', 'assistant_id');
    }

    public function removeAssistant(User $user): void
    {
        $this->assistants()->detach($user->id);
    }

    public function configFactura(): MorphMany
    {
        return $this->morphMany(ConfigFactura::class, 'facturable');
    }

    public function getObligadoTributario($office = null): ?ConfigFactura
    {
        $config = null;

        if ($this->isMedic()) {

            if ($office && $this->isOfficeFe($office->id)) {
                $config = $office->configFactura->first();
            } else {

                $office = $this->offices->where('type', OfficeType::MEDIC_OFFICE)->first();

                $config = $office?->configFactura->first();
            }
        }

        if ($this->isAssistant()) {

            $assistantUser = DB::table('assistants_users')->where('assistant_id', $this->id)->first();

            $user = User::find($assistantUser->user_id);



            if ($this->isMedicAssistant($user->id)) {


                if ($office && $user->isOfficeFe($office->id)) {

                    $config = $office->configFactura->first();
                } else {
                    $office = $user->offices->where('type', OfficeType::MEDIC_OFFICE)->first();

                    $config = $office ? $office->configFactura->first() : null;
                }
            } else {

                $clinic = $user->offices->first();

                $config = $clinic ? $clinic->configFactura->first() : null;
            }
        }

        if ($this->isClinic() || $this->isLab()) {



            $clinic = $this->offices->first();

            $config = $clinic?->configFactura->first();
        }

        return $config;
    }

    public function isMedic(): bool
    {
        return $this->isCurrentRole('medico');
    }

    public function isCurrentRole($role): bool
    {
        if (!$this->hasRole($role)) {
            return false;
        }

        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }

        return $this->currentRole && $this->currentRole->id == $role->id;
    }


    public function hasRole($role): bool
    {
        if (is_array($role)) {
            return $this->roles->whereIn('name', $role)->count();
        }

        if ($role instanceof Role) {
            $role = $role->name;
        }

        return $this->roles->contains('name', $role);
    }

    public function isOfficeFe($office): bool
    {
        if ($office instanceof Office) {
            $office = $office->id;
        }

        return DB::table('office_user')
            ->where('office_id', $office)
            ->where('user_id', $this->id)
            ->where('obligado_tributario', 'C')->exists();
    }

    public function isAssistant(): bool
    {
        return $this->hasRole('asistente');
    }

    public function isMedicAssistant($user): bool
    {
        if ($user instanceof User) {
            return $user->hasRole('medico');
        }

        return User::find($user)->hasRole('medico');
    }

    public function isClinic(): bool
    {
        return $this->hasRole('clinica');
    }

    public function isLab(): bool
    {
        return $this->hasRole('laboratorio');
    }


    public function requestOffices(): HasMany
    {
        return $this->hasMany(RequestOffice::class);
    }

    public function hasOffice($office): bool
    {
        if ($office instanceof Office) {
            $office = $office->id;
        }

        if ($this->isAssistant()) {
            return $this->clinicsAssistants->contains('id', $office);
        } else {
            return $this->offices->contains('id', $office);
        }
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointmentRequests(): HasMany
    {
        return $this->hasMany(AppointmentRequest::class, 'medic_id');
    }


    public function isClinicAssistant($user): bool
    {
        if ($user instanceof User) {
            return $user->hasRole('clinica');
        }
        return User::find($user)->hasRole('clinica');
    }

    public function isPharmacyAssistant($user): bool
    {
        if ($user instanceof User) {
            return $user->hasRole('farmacia');
        }

        return User::find($user)->hasRole('farmacia');
    }

    public function isPatient(): bool
    {
        return $this->hasRole('paciente');
    }

    public function userRole(): string
    {
        $role = 'patient';

        if ($this->isBeautician()) {
            $role = 'beautician';
        }
        if ($this->isMedic()) {
            $role = 'medic';
        }
        if ($this->isClinic()) {
            $role = 'clinic';
        }
        if ($this->isLab()) {
            $role = 'lab';
        }
        if ($this->isAssistant()) {
            $role = 'assistant';
        }

        if ($this->isPharmacy()) {
            $role = 'pharmacy';
        }

        if ($this->isOperator()) {
            $role = 'operator';
        }

        return $role;
    }

    public function isBeautician(): bool
    {
        return $this->isCurrentRole('esteticista');
    }

    public function isPharmacy(): bool
    {
        return $this->hasRole('farmacia');
    }

    public function isOperator(): bool
    {
        return $this->hasRole('operador');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function createdInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function proformas(): HasMany
    {
        return $this->hasMany(Proforma::class);
    }

    public function receptors(): HasMany
    {
        return $this->hasMany(Receptor::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function latestCierre(): HasOne
    {
        return $this->hasOne(Cierre::class)->latest();
    }

    public function cierres(): HasMany
    {
        return $this->hasMany(Cierre::class);
    }

    public function appointmentsToday(): int
    {

        return Appointment::where('created_by', $this->id)->whereDate('created_at', Carbon::Now()->toDateString())->count();
    }

    public function recalculateRatingService(): void
    {
        $reviews = $this->reviewsService()->notSpam()->approved();
        $avgRating = $reviews->avg('rating');
        $this->rating_service_cache = round($avgRating, 1);
        $this->rating_service_count = $reviews->count();

        $this->save();
    }

    public function reviewsService(): HasMany
    {
        return $this->hasMany(ReviewService::class);
    }

    public function recalculateRatingMedic(): void
    {
        $reviews = $this->reviewsMedic()->notSpam()->approved();
        $avgRating = $reviews->avg('rating');
        $this->rating_medic_cache = round($avgRating, 1);
        $this->rating_medic_count = $reviews->count();

        $this->save();
    }


    public function reviewsMedic(): HasMany
    {
        return $this->hasMany(ReviewMedic::class);
    }

    public function recalculateRatingApp(): void
    {
        $reviews = $this->reviewsApp()->notSpam()->approved();
        $avgRating = $reviews->avg('rating');
        $this->rating_app_cache = round($avgRating, 1);
        $this->rating_app_count = $reviews->count();

        $this->save();
    }

    // The way average rating is calculated (and stored) is by getting an average of all ratings,
    // storing the calculated value in the rating_cache column (so that we don't have to do calculations later)
    // and incrementing the rating_count column by 1

    public function reviewsApp(): HasMany
    {
        return $this->hasMany(ReviewApp::class);
    }

    public function hasAuthorizationOf($patient): bool
    {
        if ($patient instanceof Patient) {
            $patient = $patient->id;
        }

        return DB::table('patient_user')
            ->where('patient_id', $patient)
            ->where('user_id', $this->id)
            ->where('authorization', 1)->exists();
    }

    public function clinicsWithPermissionFe(): \Illuminate\Database\Eloquent\Collection
    {
        $allOffices = $this->offices;

        $idsClinicasPrivadaSinPermisoFE = $this->offices()
            ->where('type', OfficeType::CLINIC)
            ->where('permission_fe', 0)
            ->get()
            ->pluck('id');

        return $allOffices->whereNotIn('id', $idsClinicasPrivadaSinPermisoFE);
    }

    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class);
    }

    public function permissionCentroMedico(): bool
    {
        if ($this->belongsToCentroMedico()) {

            return DB::table('office_user')
                ->where('user_id', $this->id)
                ->where('permission_fe', 1)->exists();
        }

        return false;
    }

    public function belongsToCentroMedico(): bool
    {
        return $this->offices->where('type', OfficeType::CLINIC)->count();
    }

    public function subaccounts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'accounts', 'account_id', 'user_id');
    }


    public function assignAccount($account): array
    {
        return $this->accounts()->sync($account);
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'accounts', 'user_id', 'account_id');
    }


    public function hasAccount($account): bool
    {
        if ($account instanceof User) {
            $account = $account->id;
        }

        if ($this->isClinic() || $this->isLab()) {

            return $this->subaccounts->contains('id', $account);
        }

        return $this->accounts->contains('id', $account);
    }

    public function sendSMS($message): void
    {
        if ($this->profile->phone_number) {

            try {
                $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));



                $response = $client->messages->create(
                    // the number you'd like to send the message to
                    $this->profile->fullPhone,
                    array(
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => config('services.twilio.from'),
                        // the body of the text message you'd like to send
                        'body' => $message
                    )
                );

                Log::info($response);
            } catch (RestException | ConfigurationException | TwilioException $e) {

                Log::error($e->getMessage());
            }
        }
    }

    public function discountHistories(): HasMany
    {
        return $this->hasMany(DiscountHistory::class);
    }

    public function accumulated(): HasOne
    {
        return $this->hasOne(Accumulated::class);
    }

    public function setSettings($settings): void
    {
        foreach ($settings as $key => $value) {
            $this->settings()->updateOrCreate(
                [
                    'option' => $key,
                ],
                [
                    'option' => $key,
                    'value' => $value,
                ]
            );
        }
    }

    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user_id');
    }

    public function getAllSettings(): Collection
    {
        return $this->settings()->get()->mapWithKeys(function ($item) {
            return [$item['option'] => $item['value']];
        });
    }

    public function getSettings($settings): Collection
    {
        return $this->settings()->whereIn('option', $settings)->get()->mapWithKeys(function ($item) {
            return [$item['option'] => $item['value']];
        });
    }

    public function getSetting($key): ?string
    {
        $setting = $this->settings()->whereOption($key)->first();

        if ($setting) {
            return $setting->value;
        } else {
            return null;
        }
    }

    public function subscriptionInvoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class, 'customer_id');
    }

    public function socialImages(): HasMany
    {
        return $this->hasMany(SocialImage::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(SocialStory::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            //get: fn ($value) => strtoupper($value),
            set: fn($value) => strtoupper($value),
        );
    }

    public function affiliationUsers(): HasMany
    {
        return $this->hasMany(AffiliationUsers::class, 'user_id');
    }
    //Integrador:
    public function pharmacy()
    {
        return $this->hasOne(\App\Pharmacy::class, 'user_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Orders::class);
    }
}
