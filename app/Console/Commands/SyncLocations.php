<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Region;
use App\Models\Province;
use App\Models\District;
use App\Models\School;

class SyncLocations extends Command
{
    protected $signature = 'sync:locations';
    protected $description = 'Sincroniza regiones, provincias, distritos y escuelas desde la API y las registra en la base de datos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Iniciando sincronización de ubicaciones...');

        // Sincronizar regiones
        $this->syncRegions();

        $this->info('Sincronización completa.');
        return 0;
    }

    private function syncRegions()
    {
        $this->info('Sincronizando regiones...');
        $response = Http::withHeaders([
            'Accept' => 'application/json', // Especifica que la respuesta esperada es JSON
        ])->get('https://escale.minedu.gob.pe/padron/rest/regiones');

        if ($response->failed()) {
            $this->error('Error al obtener regiones');
            return;
        }
        $regions = $response->json()['region'];

        for ($i=0;$i<count($regions);$i++) {
            $region=$regions[$i];
            Region::updateOrCreate(
                ['idRegion' => $region['idRegion']],
                [
                    'nombreRegion' => $region['nombreRegion'],
                    'pointX' => $region['pointX'],
                    'pointY' => $region['pointY'],
                    'zoom' => $region['zoom'],
                ]
            );

            $this->syncProvinces($region['idRegion']);
        }

        $this->info('Regiones sincronizadas correctamente.');
    }

    private function syncProvinces($regionId)
    {
        $this->info("Sincronizando provincias para la región $regionId...");
        $response = Http::withHeaders([
            'Accept' => 'application/json', // Especifica que la respuesta esperada es JSON
        ])->get('https://escale.minedu.gob.pe/padron/rest/provincias', [
            'idRegion' => $regionId,
        ]);

        if ($response->failed()) {
            $this->error("Error al obtener provincias para la región $regionId");
            return;
        }
        $provinces = $response->json()['provincia'];
        if(isset($provinces['idProvincia'])){
            Province::updateOrCreate(
                ['idProvincia' => $provinces['idProvincia']],
                [
                    'nombreProvincia' => $provinces['nombreProvincia'],
                    'pointX' => $provinces['pointX'],
                    'pointY' => $provinces['pointY'],
                ]
            );
            $this->syncDistricts($provinces['idProvincia']);
        }else{
            foreach ($provinces as $province) {
                Province::updateOrCreate(
                    ['idProvincia' => $province['idProvincia']],
                    [
                        'nombreProvincia' => $province['nombreProvincia'],
                        'pointX' => $province['pointX'],
                        'pointY' => $province['pointY'],
                    ]
                );

                $this->syncDistricts($province['idProvincia']);
            }
        }


        $this->info("Provincias para la región $regionId sincronizadas correctamente.");
    }

    private function syncDistricts($provinceId)
    {
        $this->info("Sincronizando distritos para la provincia $provinceId...");
        $response = Http::withHeaders([
            'Accept' => 'application/json', // Especifica que la respuesta esperada es JSON
        ])->get('https://escale.minedu.gob.pe/padron/rest/distritos', [
            'idProvincia' => $provinceId,
        ]);

        if ($response->failed()) {
            $this->error("Error al obtener distritos para la provincia $provinceId");
            return;
        }

        $districts = $response->json()['distrito'];
        if(isset($districts['idDistrito'])){
                District::updateOrCreate(
                    ['idDistrito' => $districts['idDistrito']],
                    [
                        'nombreDistrito' => $districts['nombreDistrito'],
                    ]
                );

                $this->syncSchools($districts['idDistrito']);
        }else{
            for ($j=0;$j<count($districts);$j++) {
                $district=$districts[$j];
                District::updateOrCreate(
                    ['idDistrito' => $district['idDistrito']],
                    [
                        'nombreDistrito' => $district['nombreDistrito'],
                    ]
                );

                $this->syncSchools($district['idDistrito']);
            }
        }

        $this->info("Distritos para la provincia $provinceId sincronizados correctamente.");
    }

    private function syncSchools($districtId)
    {
        $this->info("Sincronizando escuelas para el distrito $districtId...");
        $response = Http::withHeaders([
            'Accept' => 'application/json', // Especifica que la respuesta esperada es JSON
        ])->get('https://escale.minedu.gob.pe/padron/rest/instituciones', [
            'estados' => 1,
            'ubigeo' => $districtId,
        ]);

        if ($response->failed()) {
            $this->error("Error al obtener escuelas para el distrito $districtId");
            return;
        }
        if(!isset($response->json()['items'])){
            $this->error("No se encontraron registros de escuelas");
            return;
        }
        $schools = $response->json()['items'];
        if(isset($schools['codMod'])){
            School::updateOrCreate(
                ['codMod' => $schools['codMod']],
                [
                    'idDistrito' => $districtId,
                    'nombreCenEdu' => $schools['cenEdu']?? null,
                    'cenPob' => $schools['cenPob']?? null,
                    'dirCen' => $schools['dirCen']?? null,
                    'director' => $schools['director'] ?? null,
                    'dis_crecer' => $schools['dis_crecer'] ?? null,
                    'dis_juntos' => $schools['dis_juntos'] ?? null,
                ]
            );
        }else{
            foreach ($schools as $school) {
                School::updateOrCreate(
                    ['codMod' => $school['codMod']],
                    [
                        'idDistrito' => $districtId,
                        'nombreCenEdu' => $school['cenEdu'],
                        'cenPob' => $school['cenPob'],
                        'dirCen' => $school['dirCen'],
                        'director' => $school['director'] ?? null,
                        'dis_crecer' => $school['dis_crecer'] ?? null,
                        'dis_juntos' => $school['dis_juntos'] ?? null,
                    ]
                );
            }
        }

        $this->info("Escuelas para el distrito $districtId sincronizadas correctamente.");
    }
}
