<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .container-fluid {
            margin-top: 20px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 30px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    Dear {{ $name }} <br>
                    An account has been created with below Credentials <br>
                    Please Reset your Password by clicking <a href="{{ url('/login') }}"> here</a> <br><br>
                    Site Name: <a href="{{ url('/') }}"> {{ config('app.name', 'Laravel') }}</a> <br>
                    Email: {{ $email }} <br>
                    Phone: {{ $phone }} <br>
                    Password: {{ $password }} <br><br>
                    Regards, <br>
                    Site Admin <br>
                    {{ config('app.name', 'Laravel') }} <br>
                    <small class="text-danger"><i>This is an automated message, Do not reply</i></small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>