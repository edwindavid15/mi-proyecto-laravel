<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Peluqueria;
use App\Models\Servicio;
use App\Models\User;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

     // Clientes ven solo sus citas
        if(auth()->user()->role == 'cliente'){
            $citas = Cita::where('cliente_id', auth()->id())->get();
        } else { // peluqueros o dueños ven sus citas
            $citas = Cita::where('peluquero_id', auth()->id())->get();
        }
        return view('citas.index', compact('citas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $peluquerias = Peluqueria::all();
         $servicios = Servicio::all();
         $peluqueros = User::where('role','peluquero')->get();

        return view('citas.create', compact('peluquerias','servicios','peluqueros'));
        

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'peluqueria_id' => 'required|exists:peluquerias,id',
            'servicio_id' => 'required|exists:servicios,id',
            'peluquero_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'hora' => 'required',
        ]);

        Cita::create([
            'cliente_id' => auth()->id(),
            'peluquero_id' => $request->peluquero_id,
            'peluqueria_id' => $request->peluqueria_id,
            'servicio_id' => $request->servicio_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
        ]);

        return redirect()->route('citas.index')->with('success','Cita creada correctamente');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
