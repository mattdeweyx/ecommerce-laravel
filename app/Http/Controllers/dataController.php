<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dataController extends Controller
{
    public function affichage2($nom, $prenom, $poste)
        {
            $data = [];
            $data["nom"] = $nom;
            $data["prenom"] = $prenom;
            $data["poste"] = $poste;
            $data["Modules"] = ["PHP", "LARAVEL", "HTML/CSS", "JAVASCRIPT"];
            
            return view('data')->with('data', $data);
        }

}