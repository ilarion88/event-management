<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class AttendeeController extends Controller
{
    use CanLoadRelationships;
    use AuthorizesRequests;

    private array $relations = ['user'];

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->middleware('throttle:60,1')
            ->only(['store', 'destroy']);
        $this->authorizeResource(Attendee::class, 'attendee');
    }



    public function index(Event $event)
    {
        $attendees = $this->loadRelationships(
            $event->attendees()->latest()
        );

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    public function store(Request $request, Event $event)
    {

        $attendee = $this->loadRelationships(
            $event->attendees->create([
                'user_id' => $request->user()->id
            ])
        );

        return new AttendeeResource($attendee);
    }

    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource(
            $this->loadRelationships($attendee)
        );
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Event $event, Attendee $attendee)
    {
        $attendee->delete();

        return response(status: 204);
    }
}
