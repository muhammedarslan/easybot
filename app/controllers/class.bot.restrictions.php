<?php

class EasyBotRestrictions extends GuzzleHttp\Client
{

    public function setDb($db)
    {
        //
    }

    public function setUserID($ID)
    {
        //
    }

    public function Domain($Domain)
    {
        return true;
    }

    public function Path($Path)
    {
        return true;
    }

    public function Parametres($Params)
    {
        return true;
    }
}