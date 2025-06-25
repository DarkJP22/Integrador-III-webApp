<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;


class MedicRequestController extends Controller
{
    public function __construct(UserRepository $userRepo) {

        $this->middleware('auth');
        $this->userRepo = $userRepo;

       
    }
    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $this->authorize('create', User::class);

        $search['q'] = request('q');
        $search['role'] = 'medico';
        $search['active'] = '0';

        $users = $this->userRepo->findAll($search);

        return view('admin.medics.index', compact('users', 'search'));

    }


    


}
