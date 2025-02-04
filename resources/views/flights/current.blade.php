@extends ('layouts/header')

@section('title','Laminar Data - Flights')
{{--{{phpinfo()}}--}}
@section('content')


    <h1 class="title is-1">Laminar Data - Flights</h1>
    <h2 class="subtitle">total number of records : <b>{{number_format($flights->total())}}</b></h2>
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
        <tr>
            <th>ID</th>
            <th>Flight Number</th>
            <th>Departure Airport Scheduled</th>
            <th>Airline</th>
            <th>Arrival Airport Scheduled</th>
            <th>Arrival Runwaytime Estimated</th>
            <th>Departure Runwaytime Initial</th>
            <th>Flight Status</th>
            <th>Aircraft Code</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($flights as $flight)
            <tr>

                <td>{{$flight->id}}</td>
                <td>
                    <a href="https://www.google.com/search?q={{$flight->iata_flight_number}} flight">{{$flight->iata_flight_number}}</a>
                </td>

                @if ($airport = DB::table('airports')->select('name')->where('ICAO', $flight->departure_airport_scheduled)->first())
                    <td>{{$airport->name }}</td>
                @else
                    <td>Not Available</td>
                @endif

                @if ($airline = DB::table('airlines')->select('name')->where('ICAO', $flight->airline)->first())
                    <td>{{$airline->name }}</td>
                @else
                    <td>Not Available</td>
                @endif

                @if ($airport = DB::table('airports')->select('name')->where('ICAO', $flight->arrival_airport_scheduled)->first())
                    <td>{{$airport->name }}</td>
                @else
                    <td>Not Available</td>
                @endif

                <td>{{$flight->arrival_runway_time_estimated_date}}
                    <br/>{{isset($flight->arrival_runway_time_estimated_time) ? $flight->arrival_runway_time_estimated_time : '&nbsp;' }}
                </td>
                <td>{{$flight->departure_runway_time_initial_date}}
                    <br/>{{isset($flight->departure_runway_time_initial_time) ? $flight->departure_runway_time_initial_time : '&nbsp;' }}
                </td>
                <td>{{$flight->flight_status}}</td>
                <td>{{$flight->aircraft_code}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    {{ $flights->links() }}
@endsection
