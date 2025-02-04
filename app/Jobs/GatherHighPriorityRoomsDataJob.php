<?php

namespace App\Jobs;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Rooms_high_priority_hrs_Seeder;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GatherHighPriorityRoomsDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dA;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dA)
    {
        //
        $this->dA = $dA;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {
            $room = new Rooms_high_priority_hrs_Seeder();
            $room->run($this->dA);
        } catch (Exception $e) {
            Storage::append('hrs/' . date("Y-m-d") . '/RoomsHighPriorityFailedTCJobs' . date("Y-m-d") . '.log', $e->getMessage() . ' ' . $e->getLine() . ' ' . Carbon::now()->toDateTimeString() . "\n");
        }
    }

    public function failed(Exception $e)
    {
        Storage::append('hrs/' . date("Y-m-d") . '/RoomsHighPriorityFailedJobs' . date("Y-m-d") . '.log', $e->getMessage() . ' ' . $e->getLine() . ' ' . Carbon::now()->toDateTimeString() . "\n");
    }
}
