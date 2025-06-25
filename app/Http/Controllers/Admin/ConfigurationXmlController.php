<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




class ConfigurationXmlController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        
      
    }


    /**
     * Actualizar informacion basica del medico
     */
    public function store()
    {

        $mimes = ['xml','jar'];
        $fileUploaded = 'error';

        if ($file = request()->file('xml_factura')) {

            $ext = $file->guessClientExtension() ? $file->guessClientExtension() : 'xml';

            if (in_array($ext, $mimes)) {

                $fileUploaded = $file->storeAs('facturaelectronica', 'factura.' . $ext, 'local');
            }
        }
        if ($file = request()->file('xml_nota_debito')) {


            $ext = $file->guessClientExtension() ? $file->guessClientExtension() : 'xml';

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica', 'nota_debito.' . $ext, 'local');
            }
        }
        if ($file = request()->file('xml_nota_credito')) {

            $ext = $file->guessClientExtension() ? $file->guessClientExtension() : 'xml';

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica', 'nota_credito.' . $ext, 'local');
            }
        }
        if ($file = request()->file('xml_tiquete')) {

            $ext = $file->guessClientExtension() ? $file->guessClientExtension() : 'xml';

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica', 'tiquete.' . $ext, 'local');
            }
        }
        if ($file = request()->file('xml_mensaje_receptor')) {

            $ext = $file->guessClientExtension() ? $file->guessClientExtension() : 'xml';

            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica', 'mensaje_receptor.' . $ext, 'local');
            }
        }
        if ($file = request()->file('firmador')) {

            $ext = 'jar';
           
            if (in_array($ext, $mimes)) {
                $fileUploaded = $file->storeAs('facturaelectronica', 'xadessignercrv2.' . $ext, 'local');
            }
        }

        flash('Xml subido(s) correctamente', 'success');

        return back();


    }


}
