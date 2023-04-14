<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MathPHP\Geometry\Triangulation\Delaunay;
use DB;

class CityController extends Controller
{
    public function index()
    {
        // Wählen Sie die Großstädte aus der Datenbank aus
        $result = DB::table('cities')->select('stadt', 'breite', 'laenge')->get();

        // Definieren Sie eine Menge von Punkten (Koordinaten der Großstädte)
        $points = array();
        foreach ($result as $row) {
            $points[] = array($row->breite, $row->laenge);
        }

        // Berechnen Sie die Delaunay-Triangulation
        $triangulation = Delaunay::create($points);

        // Erstellen Sie Polygone um jede Großstadt herum
        $polygons = array();
        foreach ($triangulation->getTriangles() as $triangle) {
            $vertices = array();
            foreach ($triangle as $point) {
                $vertices[] = array($points[$point][1], $points[$point][0]); // Vertices müssen im Format [Lng, Lat] sein
            }
            $polygons[] = $vertices;
        }

        // Geben Sie die Polygone als JSON zurück
        return response()->json($polygons);
    }
}
