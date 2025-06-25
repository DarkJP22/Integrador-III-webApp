<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\ReviewApp;




class ReviewAppController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
        

       
    }
    /**
     * Mostrar vista de todas las consulta(citas) de un doctor
     */
    public function index()
    {
        $this->authorize('view', ReviewApp::class);

        $reviews = ReviewApp::orderBy('created_at', 'DESC')->paginate(10);
        $avgRating = $reviews->avg('rating');

        $rating_app_cache = round($avgRating, 1);


        return view('admin.reviews.app.index', compact('reviews', 'rating_app_cache'));

    }

    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function destroy($id)
    {
        $reviewApp = ReviewApp::find($id);

        $reviewApp = $reviewApp->delete();



        return back();



    }



}
