<?php 
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class SGMailer extends Facade{
    protected static function getFacadeAccessor() { return 'sgmailerlib'; }
}