<?php

namespace App\Http\Controllers;

use App\Models\ScheduledPost;
use Illuminate\Http\Request;
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
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:280',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:twitter,reddit',
            'scheduled_date' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

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
        if (!$scheduledDate) {
            dispatch(new \App\Jobs\PublishScheduledPost($post));
        }

        return redirect()->route('dashboard')
            ->with('success', $scheduledDate
                ? 'Publicación programada correctamente para ' . date('d/m/Y H:i', strtotime($scheduledDate))
                : 'Publicación creada correctamente');
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
