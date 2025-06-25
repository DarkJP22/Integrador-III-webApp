<?php

namespace App\Http\Controllers\Medic;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class AssistantController extends Controller
{

    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('auth');
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        $this->authorize('viewAssistants', User::class);

        if (!auth()->user()->isCurrentRole('medico')) {
            return redirect('/');
        }
        
        if( !auth()->user()->subscriptionPlanHasAssistant() ){
            return redirect('/medic/changeaccounttype');
        }

        $search['q'] = request('q');

        $assistants = auth()->user()->assistants()->search($search['q'])->with('clinicsAssistants')->paginate(10);

        if (request()->wantsJson()) {
            return response($assistants, 200);
        }


        return view('medic.assistants.index', compact('assistants', 'search'));
         
    }

    public function create()
    {

        if( !auth()->user()->subscriptionPlanHasAssistant() ){
            return redirect('/medic/changeaccounttype');
        }

        return view('medic.assistants.create');
    }

    public function edit(User $assistant)
    {
        if( !auth()->user()->subscriptionPlanHasAssistant() ){
            return redirect('/medic/changeaccounttype');
        }
        $assistant->load('clinicsAssistants');
        
        return view('medic.assistants.edit', compact('assistant'));
    }
   
}
