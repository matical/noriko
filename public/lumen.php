<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Illuminate\Http\Request;
use Laravel\Lumen\Application;

putenv('APP_DEBUG=true');
$app = new Application(dirname(__DIR__) . '/');

function res(Request $request)
{
    return response()->json([
        'method'        => $request->getMethod(),
        'headers'       => $request->headers->all(),
        'query_strings' => $request->query(),
        'form_params'   => $request->request->all(),
        'json_payload'  => $request->json()->all(),
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
        'headers' => $request->headers->all(),
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

$app->router->get('from', function () {
    return redirect('to');
});

$app->router->get('to', function () {
    return 'redirected';
});

$app->run();
