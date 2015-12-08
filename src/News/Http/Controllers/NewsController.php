<?php
/**
 * @author    Mauri de Souza Nunes <mauri870@gmail.com>
 * @copyright Copyright (c) 2015, Mauri de Souza Nunes <github.com/mauri870>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace DigitalSerra\NewsLaravel\Http\Controllers;

use Illuminate\Routing\Controller;

class NewsController extends Controller
{
    public function index(){
        return 'Hello World';
    }
}