<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;

class OcController extends Controller
{
    /**
     * Lista los archivos (Órdenes de Compra) con buscador dinámico.
     */
    public function index(Request $request)
    {
        // Obtiene el término de búsqueda del input 'search'
        $search = $request->input('search');

        // Consulta el modelo Archivo cargando la relación con el usuario
        $ordenes = \App\Models\Archivo::with('user')
            // 'when' ejecuta este filtro solo si hay algo escrito en el buscador
            ->when($search, function ($query, $search) {
                // Busca por nombre del archivo
                return $query->where('nombre_original', 'like', "%{$search}%")
                    // O busca por el nombre del usuario que subió el archivo
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest() // Ordenar por creación (descendente)
            ->paginate(10); // Paginación de 10 en 10

        return view('oc.index', compact('ordenes', 'search'));
    }

    /**
     * Gestiona la descarga segura de archivos.
     */
    public function download($id)
    {
        // Busca el registro o devuelve un error 404 si no existe
        $oc = Archivo::findOrFail($id);

        // Define la ruta física donde está guardado el archivo
        $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

        // Verifica que el archivo realmente exista en el disco
        if (!file_exists($path)) {
            return back()->with('error', 'El archivo no existe en la ruta especificada.');
        }

        // Descarga el archivo usando el nombre original que tenía cuando se subió
        return response()->download($path, $oc->nombre_original);
    }

    /**
     * Previsualiza el contenido de un CSV o XML en una vista.
     */
    public function preview($id)
    {
        // 1. Obtener datos del archivo y su ruta
        $oc = Archivo::findOrFail($id);
        $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

        // 2. Extraer la extensión del nombre original para saber cómo procesarlo
        $extension = pathinfo($oc->nombre_original, PATHINFO_EXTENSION);

        if ($extension == 'csv') {
            // LÓGICA PARA CSV
            $data = [];
            // Abre el archivo en modo lectura ("r")
            if (($handle = fopen($path, "r")) !== FALSE) {
                // Lee fila por fila (fgetcsv) usando la coma como separador
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $data[] = $row; // Agrega la fila al array de datos
                }
                fclose($handle); // Cierra el archivo
            }
        } else {
            // LÓGICA PARA XML
            // Carga el archivo como un objeto de SimpleXML
            $xmlContent = simplexml_load_file($path);

            // Truco: Convierte el objeto XML a JSON y luego a un Array asociativo de PHP
            // Esto facilita mucho el recorrido de los datos en Blade
            $data = json_decode(json_encode($xmlContent), true);
        }

        // Envía los datos procesados, el objeto original y la extensión a la vista
        return view('oc.preview', compact('data', 'oc', 'extension'));
    }

    // public function previewCsv($id)

    // {

    //     $oc = Archivo::findOrFail($id);

    //     $path = storage_path('app/public/uploads' . $oc->nombre_sistema);


    //     $data = [];

    //     if (($handle = fopen($path, "r")) !== FALSE) {

    //         while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {

    //             $data[] = $row; //Guarda cada fila

    //         }

    //         fclose($handle);

    //     }


    //     return view('oc.preview_csv', compact('data', 'oc'));

    // }


    // public function previewXml($id){

    //     $oc=Archivo::findOrFail($id);

    //     $path =storage_path('app/public/uploads' . $oc->nombre_sistema);


    //     $xmlContent = simplexml_load_file($path);

    //     //Convertir  JSON y luego en Array para manejo facil en blade

    //     $data=json_decode(json_decode($xmlContent), true);


    //     return view('oc.preview_csv', compact('data', 'oc'));

    // }
}
