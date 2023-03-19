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
        {{ $flight_no }}/{{ date("d", strtotime($scheduled_time_departure)) }}.{{ $registration }}.{{ $destination }}</br>
        @if ($flight_type == 'arrival')
        AA{{ date("Hi", strtotime($touchdown)) }}/{{ date("Hi", strtotime($onblocks)) }}</br>
        @else
        AD{{ date("Hi", strtotime($offblocks)) }}/{{ date("Hi", strtotime($airborne)) }}</br>
        PX{{ $passengers }}</br>
        SI {{ strtoupper($remarks) }}
        @endif
    </div>
</body>
</html>