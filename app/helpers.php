<?php

use App\Currency;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;


function money($amount, $symbol = '₡', $decimals = 2)
{
    return (!$symbol) ? number_format($amount, $decimals, '.', ',') : $symbol . number_format($amount, $decimals, '.', ',');
}
function numberFE($amount, $decimals = 3)
{
    return  number_format($amount, $decimals, '.', '');
}

function number($amount)
{
    return preg_replace('/([^0-9\\.])/i', '', $amount);
}
function percent($amount, $symbol = '%')
{
    return $symbol . number_format($amount, 0, '.', ',');
}
function age($birthdate)
{
    return Carbon::parse($birthdate)->diff(Carbon::now())
        ->format('%y');
}
function flash($message, $level = 'info')
{
    session()->flash('flash_message', $message);
    session()->flash('flash_message_level', $level);
}
function paginate($items, $perPage)
{
    $collection = new Illuminate\Support\Collection($items);

    $page = \Request::get('page', 1);

    $offset = ($page * $perPage) - $perPage;

    $itemsForCurrentPage = $collection->slice($offset, $perPage)->values(); // se cambio por la segunda pagina retornaba un objecto en vez de un arreglo
    //$itemsForCurrentPage = array_slice($items, $offset, $perPage, true);

    $paginator = new Illuminate\Pagination\LengthAwarePaginator(
        $itemsForCurrentPage,
        count($items),
        $perPage,
        Illuminate\Pagination\Paginator::resolveCurrentPage(), // $page,
        ['path' => Illuminate\Pagination\Paginator::resolveCurrentPath()]
    );

    return $paginator;
}
/*function set_active($path, $active = 'active') {
        return call_user_func_array('Request::is', (array)$path) ? $active : '';
    }*/

function set_active($path, $active = 'active')
{
    return Request::is($path) ? $active : '';
}

function dayName($day)
{
    $dayName = '';

    if (Carbon::SUNDAY == $day) {                          // int(0)
        $dayName = 'Domingo';
    }

    if (Carbon::MONDAY == $day) {                       // int(1)
        $dayName = 'Lunes';
    }

    if (Carbon::TUESDAY == $day) {                         // int(2)
        $dayName = 'Martes';
    }

    if (Carbon::WEDNESDAY == $day) {                       // int(3)
        $dayName = 'Miércoles';
    }

    if (Carbon::THURSDAY == $day) {                       // int(4)
        $dayName = 'Jueves';
    }

    if (Carbon::FRIDAY == $day) {                          // int(5)
        $dayName = 'Viernes';
    }

    if (Carbon::SATURDAY == $day) {                        // int(6)
        $dayName = 'Sábado';
    }

    return $dayName;
}

function existsXML($xml = 'factura')
{
    $resp = false;


    if (Storage::disk('local')->exists('facturaelectronica/' . $xml . '.xml')) {
        $resp = true;
    }


    return $resp;
}

function existsFirmador($name = 'xadessignercrv2')
{
    $resp = false;


    if (Storage::disk('local')->exists('facturaelectronica/' . $name . '.jar')) {
        $resp = true;
    }


    return $resp;
}
function existsCertFile($config)
{
    $resp = false;

    $cert = 'cert';
    if ($config) {
        if (Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/' . $cert . '.p12')) {
            $resp = true;
        }
    }

    return $resp;
}
function existsCertTestFile($config)
{
    $resp = false;
    if ($config) {
        if (Storage::disk('local')->exists('facturaelectronica/' . $config->id . '/test.p12')) {
            $resp = true;
        }
    }

    return $resp;
}


function getAmountPerAppointmentAttended()
{
    $amount = 0;

    $amount = Setting::getSetting('amount_attended');

    return $amount;
}

function getAmountPerExpedientUse()
{
    $amount = 0;

    $amount = Setting::getSetting('amount_expedient');

    return $amount;
}

function getTrialConfig()
{
    $trial = 0;

    $trial = Setting::getSetting('trial_for_new_registers');

    return $trial;
}

function getCallCenterPhone()
{
    $phone = '';

    $phone = Setting::getSetting('call_center');

    return $phone;
}

function getUrlAppPacientesAndroid()
{
    $url = '';

    $url = Setting::getSetting('url_app_pacientes_android');

    return $url;
}
function getUrlAppPacientesIos()
{
    $url = '';

    $url = Setting::getSetting('url_app_pacientes_ios');

    return $url;
}
function getLabExamDiscount()
{
    return Setting::getSetting('lab_exam_discount');
}
function getUrlAppMedicosAndroid()
{
    $url = '';

    $url = Setting::getSetting('url_app_medicos_android');

    return $url;
}
function getUrlAppMedicosIos()
{
    $url = '';

    $url = Setting::getSetting('url_app_medicos_ios');

    return $url;
}

function getPorcAccumulated()
{
    $porc = 0;

    $porc = Setting::getSetting('porc_accumulated');

    return $porc;
}

function totalInvoices($invoices)
{
    $total = 0;

    foreach ($invoices as $inv) {
        $total += $inv->TotalComprobante;
    }

    return $total;
}

function fillZeroLeftNumber($value, $lenght = 9)
{
    return str_pad($value, $lenght, '0', STR_PAD_LEFT);
}
function fillZeroRightNumber($value, $lenght = 2)
{
    return $value * 100; //$value. "00";
}

function getPurchaseVerfication($purchaseOperationNumber, $purchaseAmount, $purchaseCurrencyCode)
{

    return openssl_digest(config('services.pasarela.acquire_id') . config('services.pasarela.commerce_id') . $purchaseOperationNumber . $purchaseAmount . $purchaseCurrencyCode . config('services.pasarela.clave_sha2'), 'sha512');
}

function getUniqueNumber($length = 9, $id = null)
{
    $d = date('d');
    $m = date('m');
    $y = date('Y');
    $t = time();
    $dmt = $d + $m + $y + $t;
    $ran = rand(0, 10000000);
    $dmtran = $dmt + $ran;
    if ($id) {
        $dmtran = $dmtran + $id;
    }
    //$un = uniqid();
    //$dmtun = $dmt . $un;
    //$mdun = md5($dmtran . $un);
    $sort = substr($dmtran, 0, $length);
    return $sort;
}

function is_blank($value)
{
    return empty($value) && !is_numeric($value);
}

function getWeeksOfMonth($month)
{
    $year = Carbon::now()->year;
    $date = Carbon::createFromDate($year, $month);
    $numberOfWeeks = floor($date->daysInMonth / Carbon::DAYS_PER_WEEK);
    $start = [];
    $end = [];
    $j = 0;
    for ($i = 0; $i <= $date->daysInMonth; $i++) {
        Carbon::createFromDate($year, $month, $i);
        $j++;
        $start['#' . $j . ' : ' . Carbon::createFromDate($year, $month, $i)->startOfWeek()->toDateString() . ' | ' . Carbon::createFromDate($year, $month, $i)->endOfweek()->toDateString()] = (array)Carbon::createFromDate($year, $month, $i)->startOfWeek()->toDateString();

        // $start['Week: '.$j.' Start Date']= (array)Carbon::createFromDate($year,$month,$i)->startOfWeek()->toDateString();
        // $end['Week: '.$j.' End Date']= (array)Carbon::createFromDate($year,$month,$i)->endOfweek()->toDateString();
        $i += 6;
    }
    $result = $start; //array_merge($start,$end);
    //$result['numberOfWeeks'] = ["$numberOfWeeks"];
    return $result;
}

function getPorcCommission()
{
    $porc = 0;

    $porc = Setting::getSetting('porc_commission');

    return $porc;
}

function getPorcReferenceCommission()
{
    $porc = 0;

    $porc = Setting::getSetting('porc_reference_commission');

    return $porc;
}

function getDefaultCurrency(): Currency
{
    return Currency::where('id', Setting::getSetting('app_currency') ?? 1)->first();
}

function getYearsFromDates($start, $end): array
{
    $start    = new Carbon($start);
    $end      = new Carbon($end);
    $years = range($end->year, $start->year);

    return $years;
}

function getMonths(): array
{
    return [
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre',
    ];
}


function zip_r($from, $zip, $base=false) {
    if (!file_exists($from) OR !extension_loaded('zip')) {return false;}
    if (!$base) {$base = $from;}
    $base = trim($base, '/');
    $zip->addEmptyDir($base);
    $dir = opendir($from);
    while (false !== ($file = readdir($dir))) {
        if ($file == '.' OR $file == '..') {continue;}

        if (is_dir($from . '/' . $file)) {
            zip_r($from . '/' . $file, $zip, $base . '/' . $file);
        } else {
            $zip->addFile($from . '/' . $file, $base . '/' . $file);
        }
    }
    return $zip;
}

function createZip(array $sources, $zipFile) {

    // Inicializa el archivo ZIP
    $zip = new ZipArchive;

    if ($zip->open($zipFile, ZipArchive::CREATE|ZIPARCHIVE::OVERWRITE) === TRUE) {

        // Ejemplo: agregar un directorio completo al ZIP
        foreach ($sources as $source => $name) {

            if(is_dir($source)){
                // Obtener el nombre de la carpeta contenedora (por ejemplo, "main-directory")
                $rootFolderName = '';

                if($name){
                    $rootFolderName = basename($name);
                    $zip->addEmptyDir($rootFolderName);
                }

                addDirectoryToZip($source, $zip, $rootFolderName . '/');
            }else{

                $zip->addFile($source, basename($source));
            }

        }

        // Cierra el archivo ZIP
        $zip->close();

    } else {
        throw new \Exception('Failed to create zip file');
    }
}

// Función recursiva para agregar un directorio completo al ZIP
function addDirectoryToZip($directory, $zip, $rootPath = '') {
    // Escanear los archivos y subdirectorios dentro del directorio
    $files = File::files($directory);

    // Añadir los archivos
    foreach ($files as $file) {
        // Ruta relativa dentro del ZIP
        $relativePath = $rootPath . str_replace($directory . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $zip->addFile($file->getRealPath(), $relativePath);
    }

    // Añadir los subdirectorios (si existen)
    $subdirectories = File::directories($directory);

    foreach ($subdirectories as $subdirectory) {
        // Ruta relativa para los subdirectorios
        $relativeSubdirectoryPath = $rootPath . str_replace($directory . DIRECTORY_SEPARATOR, '', $subdirectory) . '/';
        $zip->addEmptyDir($relativeSubdirectoryPath); // Crea el directorio vacío en el ZIP

        // Llamada recursiva para añadir los contenidos del subdirectorio
        addDirectoryToZip($subdirectory, $zip, $relativeSubdirectoryPath);
    }
}