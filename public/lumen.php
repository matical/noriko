<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Illuminate\Http\Request;
use Laravel\Lumen\Application;

putenv('APP_DEBUG=true');
$app = new Application(dirname(__DIR__) . '/');

function normalizeHeaders($headers)
{
    $squished = [];

    foreach ($headers as $header => $value) {
        $squished[$header] = $value[0];
    }

    return $squished;
}

function res(Request $request)
{
    return response()->json([
        'method'        => $request->getMethod(),
        'headers'       => normalizeHeaders($request->headers),
        'query_strings' => $request->query(),
        'form_params'   => $request->request->all(),
        'json_payload'  => $request->json()->all(),
        'url'           => $request->fullUrl(),
    ]);
}

$app->router->get('/', function () {
    return response('Lumen is running', 200);
});

foreach (['get', 'post', 'patch', 'put', 'delete'] as $verb) {
    $app->router->{$verb}($verb, function (Request $request) {
        return res($request);
    });
}

$app->router->get('ping', function () {
    return 'pong';
});

$app->router->post('post-multipart', function (Request $request) {
    $file = $request->file('testfile');

    return response()->json([
        'headers' => normalizeHeaders($request->headers),
        'field'   => $request->get('ksmz'),
        'file'    => [
            'filename' => $file->getClientOriginalName(),
            'content'  => file_get_contents($file->getPathname()),
        ],
    ]);
});

$app->router->get('status/{code}', function ($code) {
    return response()->json([
        'code' => $code,
    ], $code);
});

$app->router->get('header/{name}/{value}', function ($name, $value) {
    return response("$name: $value", 200, [
        $name => $value,
    ]);
});

$app->router->post('headers', function (Request $request) {
    return response()->json($request->json()->all(), 200, $request->json()->all());
});

$app->router->get('from', function () {
    return redirect('to');
});

$app->router->get('to', function () {
    return 'redirected';
});

$app->router->get('image', function () {
    $image = 'iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAYAAADE6YVjAAAACXBIWXMAAAsTAAALEwEAmpwYAAAF3WlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpIiB4bXA6Q3JlYXRlRGF0ZT0iMjAxOC0wNS0yNVQxNzowMToxMCswODowMCIgeG1wOk1vZGlmeURhdGU9IjIwMTgtMDUtMjVUMTc6MjU6MjIrMDg6MDAiIHhtcDpNZXRhZGF0YURhdGU9IjIwMTgtMDUtMjVUMTc6MjU6MjIrMDg6MDAiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiBwaG90b3Nob3A6Q29sb3JNb2RlPSIzIiBwaG90b3Nob3A6SUNDUHJvZmlsZT0ic1JHQiBJRUM2MTk2Ni0yLjEiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjM3N2Y0YjYtMmFiYi04YTQ2LWE3OTgtZTI4N2QzNjIzOWJiIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOmZlMWUwZjRhLTE5ODktN2M0Ni1hMzk1LTg0MzEyMmI1ZGY1NSIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOmZlMWUwZjRhLTE5ODktN2M0Ni1hMzk1LTg0MzEyMmI1ZGY1NSI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6ZmUxZTBmNGEtMTk4OS03YzQ2LWEzOTUtODQzMTIyYjVkZjU1IiBzdEV2dDp3aGVuPSIyMDE4LTA1LTI1VDE3OjAxOjEwKzA4OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDo2Mzc3ZjRiNi0yYWJiLThhNDYtYTc5OC1lMjg3ZDM2MjM5YmIiIHN0RXZ0OndoZW49IjIwMTgtMDUtMjVUMTc6MjU6MjIrMDg6MDAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cykiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPC9yZGY6U2VxPiA8L3htcE1NOkhpc3Rvcnk+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+yujafgAABuZJREFUSA2dwQdQFfYBwOG/bUw1ajUVL3dGhARXCGJRUVBBZKmI7Ac8HjvIdIGIgCAiKrIKyN4QeOwhAlrRaAoEC0GDCBoxbmMd0WJACmr4tem1d7krF9N8n/gFZk8UvwlTXTCnRnPNvL9YWGg+9vHc8EpfT+2CqraSXGXJ7DQhhIr4leZNf2dKTYHTfp6lf85geSs9BXI607K4lVPJ9dgKbgRmIw8IwMxLl5nK7+4S/4/fvz85Zoe3GfdSz0DbEGMVlxjL64Xcq5D7FRT0Ubjem7uecv5hU80Vr2RKkvegvmlhnxDi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';

    return response(base64_decode($image), 200, ['Content-Type' => 'image/png']);
});

$app->run();
