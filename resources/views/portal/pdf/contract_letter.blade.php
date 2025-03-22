<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ucwords($employee->name) }} - Contract Letter</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ public_path('assets/css/contract_letter.css') }}">
</head>

<body>
    <div class="contract-letter">
        @include('layouts.partials.contract_letter')

        <table class="table w-100">
            <tr>
                <td class="w-50" style="text-align: center;"></td>
                <td class="w-50" style="text-align: center;"></td>
            </tr>
            <tr>
                <td style="text-align: center; padding: 20px">
                    <hr> SIGNATURE
                </td>
                <td style="text-align: center; padding: 20px">
                    <hr> CARDINAL EMPIRE LIMITED
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <img src="{{ public_path($employee->signature) }}" alt="" style="max-height: 150px; max-width: 150px;">
                </td>
                <td style="text-align: center; color: blue;">{{ strtoupper($employee->name) }}</td>
            </tr>
            <tr>
                <td style="text-align: center; padding: 20px">
                    <hr> SIGNATURE
                </td>
                <td style="text-align: center; padding: 20px">
                    <hr> EMPLOYEE NAME
                </td>
            </tr>
        </table>
    </div>
</body>

</html>