<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to all event attendees that event starts soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();

        $eventCnt = $events->count();
        $eventLabel = Str::plural('event', $eventCnt);

        $this->info("Found {$eventCnt} {$eventLabel}");

        $events->each(
            fn ($event) => $event->attendees->each(
                fn ($attendee) => $this->info("notifying the user {$attendee->user->id}")
            )
        );

        $this->info('Reminder notifications sent successfully!');
    }
}
