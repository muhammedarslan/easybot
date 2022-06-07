<?php

class EasyDefines
{
    private static function MenuContent()
    {
        return [
            'dashboard' => [
                'MenuName' => StaticFunctions::lang('Anasayfa'),
                'MenuLink' => '/console/dashboard',
                'MenuIcon' => 'icon-home',
                'MenuLeft' => true,
                'MenuTop' => false
            ],
            'profile' => [
                'MenuName' => StaticFunctions::lang('Profil'),
                'MenuLink' => '/console/profile',
                'MenuIcon' => 'icon-check-square',
                'MenuLeft' => true,
                'MenuTop' => true
            ]

        ];
    }

    public static function TopbarContent()
    {
        $ReturnArray = [];
        foreach (self::MenuContent() as $key => $value) {
            if ($value['MenuTop'] == true) :
                $ReturnArray[$key] = $value;
            endif;
        }
        return $ReturnArray;
    }

    public static function LeftMenuContent()
    {
        $ReturnArray = [];
        foreach (self::MenuContent() as $key => $value) {
            if ($value['MenuTop'] == true) :
                $ReturnArray[$key] = $value;
            endif;
        }
        return $ReturnArray;
    }
}