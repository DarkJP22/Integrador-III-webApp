<?php

namespace App;


use App\Enums\AppointmentStatus;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Appointment extends Model
{
    use RecordsActivity;
    use HasFactory;

    protected static $recordableEvents = [
        'deleted'
    ];

    protected $fillable = [
        'patient_id', 'created_by', 'date', 'start', 'end', 'allDay', 'title', 'backgroundColor', 'borderColor',
        'medical_instructions', 'office_id', 'tracing', 'visible_at_calendar', 'finished', 'billed', 'status',
        'confirmed', 'revalorizar', 'room_id', 'optreatment_id', 'is_esthetic', 'cost',
        'available_accumulated_discount', 'total_cost', 'CodigoMoneda', 'medic_name'
    ];
    protected $casts = [
        'date' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => AppointmentStatus::class
    ];

    public function scopeSearch($query, $search)
    {
        if (isset($search['q']) && $search['q']) {

            $query = $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search['q'].'%')
                    ->orWhere('id', 'like', '%'.$search['q'].'%')
                    ->orWhereHas('patient', function ($query) use ($search) {
                        $query->where('first_name', 'like', '%'.$search['q'].'%')
                            ->orWhere('ide', 'like', '%'.$search['q'].'%');
                    });
            });
        }

        if (isset($search['start']) && $search['start']) {
            $start = new Carbon($search['start']);
            $end = (isset($search['end']) && $search['end'] != "") ? $search['end'] : $search['start'];
            $end = new Carbon($end);

            $query = $query->where([
                ['created_at', '>=', $start],
                ['created_at', '<=', $end->endOfDay()]
            ]);
        }
        if (isset($search['office']) && $search['office']) {

            $query = $query->where('office_id', $search['office']);


        }
        // return $query->where(function ($query) use ($search)
        // {
        //     $query->where('title', 'like', '%' . $search . '%')
        //             ->orWhere('id', 'like', '%' . $search . '%');

        // });
    }

    public function isStarted(): bool
    {
        return $this->status !== AppointmentStatus::SCHEDULED;
    }

    public function isFinished(): bool
    {
        return $this->finished === 1;
    }

    public function isBackgroundEvent(): bool
    {
        return $this->patient_id === 0;
    }

    public function isOwner($user = null): bool
    {
        return $this->created_by === ($user) ? $user->id : auth()->id();
    }

    public function isCreatedByPatient(): int
    {
        $user = User::find($this->created_by);


        return $user->roles()->where('name', 'paciente')->count();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function vitalSigns()
    {
        return $this->hasOne(VitalSign::class);
    }

    /* public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }*/

    public function createVitalSigns($patient_id, $vitalSigns = null)
    {

        $vitalSigns = ($vitalSigns) ? $vitalSigns : new VitalSign();
        $vitalSigns->patient_id = $patient_id;
        $vitalSigns->appointment_id = $this->id;


        return $vitalSigns->save();
    }

    public function labexams()
    {
        return $this->belongsToMany(Labexam::class);
    }

    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function reminders()
    {
        return $this->morphMany(Reminder::class, 'resource');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }

    public function createDiseaseNotes($diseaseNotes = null)
    {

        $diseaseNotes = ($diseaseNotes) ? $diseaseNotes : new DiseaseNote();


        return $this->diseaseNotes()->save($diseaseNotes);
    }

    public function diseaseNotes()
    {
        return $this->hasOne(DiseaseNote::class);
    }

    public function createPhysicalExams($physicalExams = null)
    {

        $physicalExams = ($physicalExams) ? $physicalExams : new PhysicalExam();


        return $this->physicalExams()->save($physicalExams);
    }

    public function physicalExams()
    {
        return $this->hasOne(PhysicalExam::class);
    }


    /** Estetica */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function anthropometry()
    {
        return $this->hasOne(Anthropometry::class);
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class);
    }

    public function estreatments()
    {
        return $this->hasMany(Estreatment::class);
    }

    public function recomendations()
    {
        return $this->hasMany(Recomendation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function optreatment()
    {
        return $this->belongsTo(Optreatment::class);
    }

    protected function medicName(): Attribute
    {
        return Attribute::make(
        //get: fn ($value) => strtoupper($value),
            set: fn($value) => strtoupper($value),
        );
    }

}
