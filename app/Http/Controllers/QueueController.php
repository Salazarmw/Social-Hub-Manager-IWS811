<?php

namespace App\Http\Controllers;

use App\Models\ScheduledPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    /**
     * Display a listing of the scheduled posts.
     */
    public function index(Request $request)
    {
        $query = ScheduledPost::where('user_id', Auth::id());
        
        // Filtrar por estado si se proporciona
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filtrar por fecha si se proporciona
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }
        
        // Ordenar por fecha de programación (más reciente primero)
        $posts = $query->orderBy('scheduled_date', 'desc')->paginate(10);
        
        return view('queue.index', [
            'posts' => $posts,
            'filters' => $request->only(['status', 'date_from', 'date_to'])
        ]);
    }
    
    /**
     * Remove the specified scheduled post.
     */
    public function destroy($id)
    {
        $post = ScheduledPost::where('user_id', Auth::id())->findOrFail($id);
        $post->delete();
        
        return redirect()->route('queue.index')
            ->with('success', 'Publicación programada eliminada correctamente');
    }
}
