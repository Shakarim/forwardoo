<?php

namespace App\Modules\Cargo\Enums;

enum Stacking: string
{
    case OnlyBottom = 'OnlyBottom';
    case OnlyTop = 'OnlyTop';
    case AnyStacking = 'AnyStacking';
    case NoStacking = 'NoStacking';
}
