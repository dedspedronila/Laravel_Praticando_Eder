<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(){
    $nome = "André";
    $idade =20;
    $arr = [10,20,30,40,50];

    $nomes = ["André", "Maria", "João", "Ana"];

    return view('welcome',
    [
        'nome' => $nome,
        'idade' => $idade,
        'arr' => $arr,
        'nomes' => $nomes
    ]);

    }

    public function create() {
        return view('events.create');
    }
}