<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: {{ config('laradium-documents.font_family', 'DejaVu Sans, serif') }};
        }
    </style>
</head>
<body>
{!! $content !!}
</body>
</html>
