<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Event::class);
        $query = $this->loadRelationships(Event::query());

        return EventResource::collection($query->latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Event::class);
        $event = Event::create([
            ...$request->validate([
                'name' => 'required | string | max:255',
                'description' => 'nullable | string',
                'start_at' => 'required | date',
                'end_at' => 'required | date | after:start_at',
            ]),
            'user_id' => $request->user()->id
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        Gate::authorize('view', $event);
        $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);
        $event->update([
            ...$request->validate([
                'name' => 'sometimes | string | max:255',
                'description' => 'nullable | string',
                'start_at' => 'sometimes | date',
                'end_at' => 'sometimes | date | after:start_at',
            ])
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);
        $event->delete();

        return response(status: 204);
    }
}
