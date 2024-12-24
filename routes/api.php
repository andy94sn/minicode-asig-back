<?php

use Illuminate\Support\Facades\Route;
use Rebing\GraphQL\Support\Facades\GraphQL;

Route::post('graphql', function () {
    return GraphQL::executeQuery(request('query'));
});
