<?php

namespace App\Http\Controllers\Api;

use App\Enums\OfficeType;
use App\Http\Controllers\Controller;
use App\Office;

class LaboratoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {

        $labs = Office::query()
            ->where('type', OfficeType::LABORATORY)
            ->where('active', 1)
            ->search(request('q'))
            ->when(request('lat'), function ($query) {
                $query->nearLatLng(request('lat'), request('lon'), request('radius'));
            })
            ->when(request('province'), function ($query) {
                $query->where('province', request('province'));
            })
            ->when(request('canton'), function ($query) {
                $query->where('province', request('province'))
                    ->where('canton', request('canton'));
            })
            ->when(request('district'), function ($query) {
                $query->where('province', request('province'))
                    ->where('canton', request('canton'))
                    ->where('district', request('district'));
            })
            ->paginate();

        return $labs;
    }

    public function show(Office $office): Office
    {
        if (request('lat') && request('lon')) {
            $office->nearLatLng(request('lat'), request('lon'), 300);
        }

        return $office;
    }
}
