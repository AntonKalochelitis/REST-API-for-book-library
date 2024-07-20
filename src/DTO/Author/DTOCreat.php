<?php

namespace App\DTO\Author;

use App\Traits\DTO\Author\FirstName;
use App\Traits\DTO\Author\LastName;
use App\Traits\DTO\Author\Patronymic;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic
 */
class DTOCreat
{
    use FirstName;
    use LastName;
    use Patronymic;
}