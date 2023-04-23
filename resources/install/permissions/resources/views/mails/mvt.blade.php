<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div>
        MVT</br>
        {{ $flt->flight_no }}/{{ ($flt->flight_type == 'arrival') ? 
            date("d", strtotime($flt->scheduled_time_arrival)) . "." . $flt->registration . "." . $flt->destination : 
            date("d", strtotime($flt->scheduled_time_departure)) . "." . $flt->registration . "." . $flt->origin }}</br>
        @if ($flt->flight_type == 'arrival')
        AA{{ date("Hi", strtotime($mvt->touchdown)) }}/{{ date("Hi", strtotime($mvt->onblocks)) }}</br>
        @else
        AD{{ date("Hi", strtotime($mvt->offblocks)) }}/{{ date("Hi", strtotime($mvt->airborne)) }}
        EA{{ date("Hi", strtotime($mvt->airborne) + strtotime($mvt->flight_time ?? 0)) }} {{ $flt->destination }}</br>
        @if (empty($outputdelay))  @else {{ "DL". $outputdelay ?? null }}</br> @endif
        PX{{ $mvt->passengers ?? 00 }}</br>
        @if (empty($outputedelay))  @else {{ "EDL" .$outputedelay ?? null }}</br> @endif
        @if (empty($outputdescription))  @else {!! "SI ". nl2br(e($outputdescription)) ?? null !!}</br> @endif
        @if (empty($mvt->remarks))  @else SI {{ strtoupper($mvt->remarks ?? null) }}</br> @endif
        SI EET {{ date("Hi", strtotime($flightTime ?? 0)) }} HRS
        @endif
    </div>
</body>
</html>