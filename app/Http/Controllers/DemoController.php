<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DemoRequest;

class DemoController extends Controller
{
    public function __invoke(DemoRequest $request)
    {
        return 'ok';
    }
}
