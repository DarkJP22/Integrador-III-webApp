<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;

class AssistantController extends Controller
{

    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('auth');
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        return auth()->user()->assistants()->with('clinicsAssistants')->paginate(10);
    }
    /**
     * Actualizar informacion basica del medico
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],
            'office_id' => 'required',
        ]);

        $data = request()->all();

        $data['role'] = Role::whereName('asistente')->first();
        $data['fe'] = auth()->user()->fe;
        
        $user = $this->userRepo->store($data);

      
        $user->clinicsAssistants()->sync([$data['office_id']]);

        auth()->user()->addAssistant($user);


        return $user->load('clinicsAssistants');
    }

    /**
     * Actualizar informacion basica del medico
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => ['required', 'digits_between:8,15'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'office_id' => 'required',
        ]);

        $data = request()->all();

        $assistant = $this->userRepo->update($id, $data);

        $assistant->clinicsAssistants()->sync([$data['office_id']]);

        return $assistant->load('clinicsAssistants');
    }

    /**
     * Actualizar informacion basica del medico
     */
    public function destroy($id)
    {
        $assistant = User::find($id);

        auth()->user()->removeAssistant($assistant);

        $assistant->delete();

        if (request()->wantsJson()) {

            return response([], 204);

        }

        return back();
    }
}
