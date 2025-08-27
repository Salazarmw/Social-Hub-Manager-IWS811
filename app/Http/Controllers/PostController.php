<?php

namespace App\Http\Controllers;

use App\Models\ScheduledPost;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Auth;
use App\Jobs\PublishScheduledPost;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {

        $scheduledDate = $request->input('scheduled_date');

        // Determinar el estado basado en si hay una fecha programada
        $status = $scheduledDate ? 'pending' : 'processing';

        // Crear la publicación programada
        $post = ScheduledPost::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'platforms' => $request->input('platforms'),
            'scheduled_date' => $scheduledDate,
            'status' => $status,
        ]);

        // Si no hay fecha programada, despachar el job inmediatamente
        try {
            if (!$scheduledDate) {
                dispatch(new \App\Jobs\PublishScheduledPost($post));
                return redirect()->route('dashboard')
                    ->with('success', '¡La publicación se ha enviado correctamente y se está procesando!');
            }

            return redirect()->route('dashboard')
                ->with('success', sprintf(
                    '¡Publicación programada correctamente para el %s!',
                    date('d/m/Y \a \l\a\s H:i', strtotime($scheduledDate))
                ));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Ha ocurrido un error al procesar la publicación. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
