<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __invoke()
    {
        $this->validate($this->request, [
            // rules
        ]);

        $this->request->validate([
            //  rules
        ]);

        dd(get_class_methods($this));
        dd($this->user, $this->request);
    }
}
