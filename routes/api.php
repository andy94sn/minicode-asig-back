<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Rebing\GraphQL\Support\Facades\GraphQL;

Route::post('payment/callback', [PaymentController::class, 'callback']);
Route::post('graphql', function () {
    return GraphQL::executeQuery(request('query'));
});
