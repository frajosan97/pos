<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details['subject'] }}</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; color: #333333;">
    <div style="max-width: 600px; margin: 40px auto; background-color: #ffffff; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

        <div style="padding: 10px; text-align: center;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="">
        </div>

        <!-- Header -->
        <div style="background-color: rgb(201, 40, 0); color: #ffffff; padding: 5px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">{{ $details['title'] }}</h1>
        </div>

        <!-- Content -->
        <div style="padding: 20px; line-height: 1.6;">
            <p style="margin: 15px 0;">{{ $details['body'] }}</p>

            <!-- Additional Information -->
            @if(isset($details['more_info']) && !empty($details['more_info']))
            <p style="margin: 15px 0;">More Information:</p>
            <ul style="padding-left: 20px; margin: 15px 0;">
                @foreach($details['more_info'] as $key => $info)
                <li style="margin: 10px 0;"><strong>{{ $key }}:</strong> {!! $info !!}</li>
                @endforeach
            </ul>
            @endif

            <p style="margin: 15px 0;">{{ $details['footer'] }}</p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f4f4f4; text-align: center; padding: 10px; font-size: 14px; color: #666666;">
            <p style="margin: 5px 0;">If you have any questions, please contact us at</p>
            <p>
                <a href="mailto:{{ $company_info['email'] }}" style="color: #007BFF; text-decoration: none;">
                    {{ $company_info['email'] }}
                </a>
            </p>
            <p>{{ $company_info['phone'] }}</p>
            <p style="margin: 5px 0;">&copy; {{ date('Y') }} {{ ucwords($company_info['name']) }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>