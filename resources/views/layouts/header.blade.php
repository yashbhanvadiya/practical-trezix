<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Practical Trezix</title>
    <link href="{{ URL::to('https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css') }}"
        rel="stylesheet">
    <link href="{{ URL::to('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css') }}"
        rel="stylesheet" />
    <link href="{{ URL::to('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css') }}"
        rel="stylesheet">

    @yield('css')
</head>

<body>
