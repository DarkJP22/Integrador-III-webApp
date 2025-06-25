<?php namespace App\Repositories;


use App\Appointment;
use App\Balance;
use App\Income;
use App\Office;
use App\Patient;
use App\Repositories\IncomeRepository;
use App\User;
use Carbon\Carbon;

class AppointmentRepository extends DbRepository
{


    /**
     * Construct
     * @param User $model
     */
    function __construct(Appointment $model, IncomeRepository $incomeRepo)
    {
        $this->model = $model;
        $this->incomeRepo = $incomeRepo;
        $this->limit = 10;
    }

    /**
     * save a appointment
     * @param $data
     */
    public function store($data, $user_id = null)
    {


        $medic = User::find(($user_id) ? $user_id : auth()->id()); // buscar doctor

        if($medic->isCurrentRole('esteticista')) $data['is_esthetic'] = 1;

        $data['created_by'] = auth()->id(); // asignar el usuario que creo la cita, ya sea doctor o paciente
        $data['medic_name'] = $medic->name;
        $patient = Patient::find($data['patient_id']);
        $office = Office::find($data['office_id']);
       
        if ($patient) {
            $appointment = $medic->appointments()->create($data); //$this->model->create($data);


            $appointment->patient()->associate($patient); // asociar la cita con el paciente
            $appointment->save();

            if(!$medic->patients()->where('patients.id', $patient->id)->exists()){
                $medic->patients()->attach($patient, ['authorization' => 1]); // asociar la medico con el paciente si no existe
            }

            if($office?->exists() && !$office->patients()->where('patients.id', $patient->id)->exists()){
                $office->patients()->attach($patient, ['authorization' => 1]); // asociar la clinica con el paciente si no existe
            }

        } else {

            if ($data['patient_id']) // verificar si lo que viene es 0 o un numero que no existe por lo cual enviar mensaje de error o crear la cita como background
            {

                return false;

            }

            $appointment = $medic->appointments()->create($data); //$this->model->create($data);


        }

        $appointment->createDiseaseNotes();
        $appointment->createPhysicalExams();
        $appointment->createVitalSigns($patient->id);
          
      
        return $appointment;

    }

    /**
     * Update a appointment
     * @param $id
     * @param $data
     * @return \Illuminate\Support\Collection|static
     */
    public function update($id, $data)
    {
        $appointment = $this->model->find($id);
       
        if (!$appointment) return '';

        if (auth()->user()->hasRole('paciente')) {

            if ($appointment->isStarted() || $appointment->isBackgroundEvent() || !$appointment->isOwner()) {

                return '';
            }

        } else {

            if ($appointment->isStarted() && isset($data['date'])) {
                return '';
            }
        }


        $appointment->fill($data);
        $appointment->save();

        $patient = Patient::find(isset($data['patient_id']) ? $data['patient_id'] : 0);

        if ($patient) {

            $appointment->patient()->associate($patient); // asociar la cita con el paciente
            $appointment->save();

        }

        return $appointment;


    }

    public function update_status($id, $status)
    {

        $appointment = $this->findById($id);

        // if ($appointment->status == 0) {

        //     if ($appointment->isCreatedByPatient())
        //         $dataIncome['type'] = 'I'; // cobro por cita generada de paciente
        //     else
        //         $dataIncome['type'] = 'P'; // pendiente

        //     $dataIncome['medic_type'] = auth()->user()->specialities->count() > 0 ? 'S' : 'G';
        //     $dataIncome['amount'] = getAmountPerAppointmentAttended();
        //     $dataIncome['appointment_id'] = $appointment->id;
        //     $dataIncome['office_id'] = $appointment->office_id;
        //     $dataIncome['date'] = $appointment->date;
        //     $dataIncome['month'] = $appointment->date->month;
        //     $dataIncome['year'] = $appointment->date->year;



        //     $income = $this->incomeRepo->store($dataIncome);
        // }

        if ($appointment->status == 2) { // si esta finalizada no la actualizamos
            return $appointment;
        }

        $appointment->status = $status;
        $appointment->save();

        return $appointment;
    }



    public function delete($id)
    {
        $appointment = $this->model->find($id);

        if (!$appointment) return -1;

        if (auth()->user()->hasRole('paciente')) {

            if (!$appointment->isStarted() && $appointment->isOwner()) {
                $appointment->reminders()->delete();
                $appointment->income()->delete();
                return $appointment = $appointment->delete();
            }
        } else {

            if (!$appointment->isStarted()) {
                $appointment->reminders()->delete();

                return $appointment = $appointment->delete();
            }
        }


        return $appointment;
    }


    /**
     * Find all the appointments by Doctor
     * @internal param $username
     * @param null $search
     * @return mixed
     */
    public function findAllByDoctor($id, $search = null, $limit = 10)
    {
        $order = 'date';
        $dir = 'desc';

        $appointments = $this->model->where('user_id', $id)->where('patient_id', '<>', 0);

        if (!$search) return $appointments->with('user', 'patient', 'notes')->orderBy('appointments.' . $order, $dir)->orderBy('appointments.start', $dir)->paginate($limit);

        if (isset($search['q']) && trim($search['q'] != "")) {

            $appointments = $appointments->Search($search['q']);
        }
        if (isset($search['office']) && $search['office'] != "") {
            $appointments = $appointments->where('office_id', $search['office']);
        }
        if (isset($search['calendar']) && $search['calendar'] != "") {
            $appointments = $appointments->where('visible_at_calendar', $search['calendar']);
        }

        if (isset($search['date']) && $search['date'] != "") {

            $appointments = $appointments->whereDate('date', $search['date']);
        }


        if (isset($search['order']) && $search['order'] != "") {
            $order = $search['order'];
        }
        if (isset($search['dir']) && $search['dir'] != "") {
            $dir = $search['dir'];
        }


        return $appointments->with('user', 'patient', 'notes')->orderBy('appointments.' . $order, $dir)->orderBy('appointments.start', $dir)->paginate($limit);

    }
    /**
     * Find all the appointments by Doctor
     * @internal param $username
     * @param null $search
     * @return mixed
     */
    public function findAllByDoctorWithoutPagination($id, $search = null)
    {
        $order = 'created_at';
        $dir = 'desc';

        $appointments = $this->model->where('user_id', $id);

        if (!$search) return $appointments->with('patient', 'user', 'office')->get();

        if (isset($search['q']) && trim($search['q'])) {
            $appointments = $appointments->Search($search['q']);
        }

        if (isset($search['office']) && $search['office'] != "") {
            $appointments = $appointments->where('office_id', $search['office']);
        }

        if (isset($search['calendar']) && $search['calendar'] != "") {
            $appointments = $appointments->where('visible_at_calendar', $search['calendar']);
        }

        if (isset($search['date1']) && $search['date1'] != "") {
           
           // dd($search['date2']);

            $date1 = $search['date1'];
            $date2 = (isset($search['date2']) && $search['date2'] != "") ? $search['date2'] : $search['date1'];
            $date2 = $date2;


            $appointments = $appointments->where([
                ['appointments.date', '>=', $date1],
                ['appointments.date', '<=', $date2->endOfDay()]
            ]);

        }


        if (isset($search['order']) && $search['order'] != "") {
            $order = $search['order'];
        }
        if (isset($search['dir']) && $search['dir'] != "") {
            $dir = $search['dir'];
        }


        return $appointments->with('patient', 'user', 'office')->orderBy('appointments.' . $order, $dir)->get();

    }
    /**
     * Find all the appointment by Patient
     * @internal param $username
     * @param null $search
     * @return mixed
     */
    public function findAllByPatient($id, $search = null)
    {
        $order = 'date';
        $dir = 'desc';

        $appointments = $this->model->where('patient_id', $id);

        if (!$search) return $appointments->with('user')->paginate($this->limit);

        if (isset($search['q']) && trim($search['q'])) {
            $appointments = $appointments->Search($search['q']);
        }
        if (isset($search['office']) && $search['office'] != "") {
            $appointments = $appointments->where('office_id', $search['office']);
        }
        if (isset($search['status']) && $search['status'] == 1) {
            $appointments = $appointments->where('status', '>=', 1);
        }

        if (isset($search['status']) && $search['status'] == 0) {
            $appointments = $appointments->where('status', 0);
        }
        if (isset($search['calendar']) && $search['calendar'] != "") {
            $appointments = $appointments->where('visible_at_calendar', $search['calendar']);
        }


        if (isset($search['order']) && $search['order'] != "") {
            $order = $search['order'];
        }
        if (isset($search['dir']) && $search['dir'] != "") {
            $dir = $search['dir'];
        }


        return $appointments->with('user', 'notes')->orderBy('appointments.' . $order, $dir)->paginate($this->limit);

    }

  

   /*   public function findById($id)
    {
        return $this->model->with('diseaseNotes')->findOrFail($id);
    }*/

    private function prepareData($data)
    {


        return $data;
    }


}