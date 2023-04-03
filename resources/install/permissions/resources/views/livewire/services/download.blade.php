<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <style>
        body {
        border: 1px solid black;
        margin: 0;
        padding: 0;
        }

        .card-body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding-left: 10px;
            padding-right: 10px;
            border: 1px solid #dee2e6;
        }

        .table-responsive {
        overflow-x: auto;
        }

        table {
        width: 100%;
        margin-bottom: 0;
        color: #333;
        background-color: transparent;
        border-collapse: collapse;
        }

        thead th {
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        }

        th, td {
        padding: .3rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
        }

        tbody td:first-child {
        font-weight: 600;
        }

        td[colspan="100%"] {
        text-align: center;
        }
    </style>
</head>
<body>
    <div class="card-body">
        <div style="text-align: center; margin: 1rem;">
            <h3 style="margin: 1px;"> {{ $selectedFlight->flight_no }} - {{ $selectedFlight->registration }}</h3>
            {{ $selectedFlight->destination }} {{ date('H:s', strtotime($selectedFlight->scheduled_time_arrival)) }}
            {{ $selectedFlight->origin }} {{ date('H:s', strtotime($selectedFlight->scheduled_time_departure)) }} <br>
            Work-Oder No: {{ preg_replace('/\b(\w)\w*\s*/', '$1', ucwords(config('app.name', 'Laravel'))) }}{{ str_pad($selectedFlight->id, 6, '0', STR_PAD_LEFT) }} <br>
            Date: {{ $selectedFlight->created_at->format('d M, Y') }}
        </div>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Start</th>
                        <th>Finish</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($selectedFlight->service as $index => $service)
                        <tr>
                            <td>{{ $index + 1 }}. {{ $service->service_type }}</td>
                            <td>{{ $service->start }}</td>
                            <td>{{ $service->finish }}</td>
                            <td>{{ date('H:i', strtotime($service->finish) - strtotime($service->start)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%">No Services for this flight Yet</td>
                        </tr>
                    @endforelse
                    @if(count($selectedFlight->service) > 0)
                        <tr>
                            <td colspan="3"><strong>Total Services</strong></td>
                            <td colspan="2"><strong>{{ count($selectedFlight->service) }} Services</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>    
</body>
</html>