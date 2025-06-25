<?php

namespace App;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;

class ConfigFactura extends Model
{
    protected $fillable = ['nombre', 'nombre_comercial', 'tipo_identificacion', 'identificacion', 'sucursal', 'pos', 'codigo_pais_tel', 'telefono', 'codigo_pais_fax', 'fax', 'provincia', 'canton', 'distrito', 'barrio', 'otras_senas', 'email', 'consecutivo_inicio', 'atv_user', 'atv_password', 'pin_certificado', 'consecutivo_inicio_ND', 'consecutivo_inicio_NC', 'grant_type', 'access_token', 'refresh_token', 'token_expires_at','refresh_expires_at','consecutivo_inicio_tiquete','consecutivo_inicio_receptor'];
    protected $table = 'config_facturas';

    public $timestamps = false;

    protected $appends = ['certificadoInstalado'];

    public function getCertificadoInstaladoAttribute()
    {
        return existsCertFile($this);
    }

    public function setAtvPasswordAttribute($value)
    {
        $this->attributes['atv_password'] = encrypt($value);
    }
    public function setPinCertificadoAttribute($value)
    {
        $this->attributes['pin_certificado'] = encrypt($value);
    }
    public function getAtvPasswordAttribute($value)
    {
       
        try {
            $decrypted = decrypt($value);
        } catch (DecryptException $e) {
            $decrypted = null;
            \Log::error('Error en desencriptar el AtvPassword');
        }
        return $decrypted;
    }
    public function getPinCertificadoAttribute($value)
    {
        try {
            $decrypted = decrypt($value);

        } catch (DecryptException $e) {
            $decrypted = null;
            \Log::error('Error en desencriptar el PinCertificado');
        }
        return $decrypted;
    }



    public function facturable()
    {
        return $this->morphTo();
    }

    public function activities()
    {
        return $this->hasMany(EmisorActivity::class);
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed $role
     * @return boolean
     */
    public function hasActivity($activity)
    {
        if ( $activity instanceof Activity ) {
            $activity = $activity->codigo;
        }

        return $this->activities->contains('codigo', $activity);
    }

    public function saveActivities($items)
    {
       
        $actividades = Activity::whereIn('codigo', $items)->get();
     
        foreach ($actividades as $item) {

         
             $this->activities()->create(['codigo' => $item->codigo, 'actividad' => $item->actividad]);
            
           
        }

        $this->CodigoActividad =  Optional($this->activities->first())->codigo;
        $this->save();
        
        return $this;
    }
}
