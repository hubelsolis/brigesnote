<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Province;
use App\Models\District;
use App\Models\School;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // Obtener todas las regiones desde la base de datos
    public function getRegions()
    {
        $regions = Region::all();

        return response()->json([
            'status' => 'success',
            'data' => $regions,
        ]);
    }

    // Obtener provincias de una región (según los dos primeros caracteres de idDistrito)
    public function getProvinces($idRegion)
    {
        // Filtrar provincias cuyo idDistrito comienza con los mismos caracteres de idRegion
        $provinces = Province::whereRaw('LEFT(idProvincia, 2) = ?', [$idRegion])->get();

        if ($provinces->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron provincias para esta región.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $provinces,
        ]);
    }

    // Obtener distritos de una provincia (según los cuatro primeros caracteres de idDistrito)
    public function getDistricts($idProvince)
    {
        // Filtrar distritos cuyo idDistrito comienza con los mismos caracteres de idProvincia
        $districts = District::whereRaw('LEFT(idDistrito, 4) = ?', [$idProvince])->get();

        if ($districts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron distritos para esta provincia.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $districts,
        ]);
    }

    // Obtener escuelas de un distrito (según idDistrito completo)
    public function getSchools($districtId)
    {
        $schools = School::where('idDistrito', $districtId)->get();

        if ($schools->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron escuelas para este distrito.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $schools,
        ]);
    }
}
