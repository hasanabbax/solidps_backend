<?php

use App\Jobs\GatherRoomsDataJob;
//use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class Rooms_hrs_Queues_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //empty job queue before sending new jobs.
        $notReservedJobs = DB::table('jobs')->select('id')->where('queue','=','default')->get();
        foreach($notReservedJobs as $notReservedJob){
            DB::table('jobs')->where('id', '=', $notReservedJob->id)->delete();
        }

        $dA['currency'] = 'EUR';
        $dA['adults'] = [2];
        $dA['start_date'] = date("Y-m-d");
        $dA['end_date'] = date("Y-m-d", strtotime("+365 day"));


        while (strtotime($dA['start_date']) <= strtotime($dA['end_date'])) {
            $dA['check_in_date'] = $dA['start_date'];
            $dA['check_out_date'] = date("Y-m-d", strtotime("+1 day", strtotime($dA['start_date'])));


            GatherRoomsDataJob::dispatch($dA)->delay(now()->addSecond(2));

            if ($dA['start_date'] < date("Y-m-d", strtotime("+180 day"))) {
                $dA['start_date'] = date("Y-m-d", strtotime("+1 day", strtotime($dA['start_date'])));
            } else {
                $dA['start_date'] = date("Y-m-d", strtotime("+7 day", strtotime($dA['start_date'])));
            }
        }

    }
}
