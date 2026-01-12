<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Guest Portal Access</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f6f9; padding: 20px; margin: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h2 { color: #333333; margin-bottom: 20px; }
        p { color: #555555; line-height: 1.6; font-size: 16px; margin-bottom: 15px; }
        .btn { display: inline-block; background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 25px; margin-bottom: 25px; }
        .footer { margin-top: 40px; border-top: 1px solid #eeeeee; padding-top: 20px; font-size: 12px; color: #999999; text-align: center; }
        .info-box { background: #f0f9ff; border-left: 5px solid #0ea5e9; padding: 20px; margin: 25px 0; border-radius: 4px; }
        .label { font-weight: bold; color: #0c4a6e; display: block; margin-bottom: 5px; }
        .value { font-size: 18px; color: #0284c7; font-weight: bold; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Hello, {{ $lead->name }}</h2>
    
    <p>We are getting everything ready for your upcoming trip!</p>
    
    <p>Please access your private Guest Portal to review your invoice, update your personal details, and add the details of your fellow travelers.</p>

    <div class="info-box">
        <span class="label">Login Credentials:</span>
        Your Password is your registered Mobile Number:<br>
        <span class="value">{{ $lead->phone_number }}</span>
    </div>

    <div style="text-align: center;">
        <a href="{{ $url }}" class="btn">Access My Guest Portal</a>
    </div>

    <p style="margin-top: 30px; font-size: 14px;">If the button above does not work, please copy and paste this link into your browser:</p>
    <p style="font-size: 12px; color: #2563eb; word-break: break-all;">
        <a href="{{ $url }}" style="color: #2563eb;">{{ $url }}</a>
    </p>

    <div class="footer">
        &copy; {{ date('Y') }} Tourism Company. All rights reserved.<br>
        <span style="color: #cccccc;">Automated System Notification</span>
    </div>
</div>

</body>
</html>