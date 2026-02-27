<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //   
           // Mostrar todos los servicios del usuario según rol
     if(auth()->user()->isPeluquero()) {
        $servicios = Servicio::where('peluquero_id', auth()->id())->get();
      } elseif(auth()->user()->isDueno()) {
        // Servicios de su peluquería
        $servicios = Servicio::where('peluqueria_id', auth()->user()->peluquerias->pluck('id'))->get();
      } else {
        // Clientes ven todos los servicios disponibles
        $servicios = Servicio::all();
      }

     return view('servicios.index', compact('servicios'));
     
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

            if(!auth()->user()->isPeluquero() && !auth()->user()->isDueno()){
        abort(403);
        }

     $peluquerias = auth()->user()->isDueno() ? auth()->user()->peluquerias : [];
      return view('servicios.create', compact('peluquerias'));
      
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //


               $request->validate([
        'nombre' => 'required|string|max:255',
        'precio' => 'required|numeric',
       ]);

      Servicio::create([
     'nombre' => $request->nombre,
     'descripcion' => $request->descripcion,
     'precio' => $request->precio,
     'duracion' => $request->duracion,
     'peluquero_id' => auth()->id(),
       ]);

     return redirect()->route('servicios.index')->with('success', 'Servicio creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
     {
     $servicio = Servicio::findOrFail($id);
     return view('servicios.show', compact('servicio'));
     }

    /**
     * Show the form for editing the specified resource.
     */
           public function edit($id)
         {
           $servicio = Servicio::findOrFail($id);
          return view('servicios.edit', compact('servicio'));
          }

    /**
     * Update the specified resource in storage.
     */
      public function update(Request $request, $id)
     {
       $servicio = Servicio::findOrFail($id);

         $servicio->update([
         'nombre' => $request->nombre,
         'descripcion' => $request->descripcion,
         'precio' => $request->precio,
         'duracion' => $request->duracion,
          ]);

             return redirect()->route('servicios.index')
                     ->with('success', 'Servicio actualizado correctamente');
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
