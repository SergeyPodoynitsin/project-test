<?php

namespace App\Enums;

enum StatusEnum: string {
    case New = 'Новая';
    case Discussion = 'Обсуждение';
    case Progress = 'Прогресс';
    case Review = 'Пересмотр';
    case Done = 'Закончена';
}


?>