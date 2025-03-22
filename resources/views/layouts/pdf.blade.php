<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <!-- Use absolute URL for the CSS file -->
    <link rel="stylesheet" href="{{ public_path('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ public_path('assets/css/pdf.css') }}">
</head>

<body>

    <!-- Letter head -->
    <div class="letterhead">
        <table>
            <tr>
                <!-- Logo Section -->
                <td style="width: 20%; text-align: left; vertical-align: middle;">
                    <div class="logo">
                        <img src="{{ public_path('assets/images/logo/'.$company_info->logo) }}" alt="" srcset="">
                    </div>
                </td>

                <!-- Business Information Section -->
                <td style="width: 60%; text-align: center; vertical-align: middle;">
                    <div class="business-info">
                        <h2>{{ $company_info->name }}</h2>
                        <h5>{{ $company_info->address }}</h5>
                        <p>Tel: {{ $company_info->phone }} | Email: <span style="color: blue;">{{ $company_info->email }}</span></p>
                    </div>
                </td>

                <!-- Date and Time Section -->
                <td style="width: 20%; text-align: right; vertical-align: middle;">
                    <div class="date-time">
                        <p>Date: {{ date('d/m/Y') }}</p>
                        <p>Time: {{ date('h:i A') }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- PDF Title -->
    <div class="pdf-title">
        <h5 class="m-0 p-1 bg-light text-center text-uppercase">@yield('title')</h5>
    </div>

    @yield('content')

</body>

</html>