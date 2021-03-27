<?php


namespace App\Http\Controllers\Home\Navigation;

use App\Http\Controllers\Controller;

use App\Http\Requests\AppRequest;
use App\Lib\Navigation\NavigationEngine;
use Illuminate\Support\Facades\Auth;


class NavigationController extends Controller
{

    public function __construct(private AppRequest $req)
    {
    }


    public function show()
    {
        /*$navigationEngine = new NavigationEngine();

        $blocks = $navigationEngine->getBlocksForRoles(auth_user()->getRoles());


        $input = $this->req->unpackInput(['b', 'v']);


        if ($input === false) {

            // Генерация default_view
        } else {

            //
        }*/




        return view('pages.home.navigation');
    }
}
