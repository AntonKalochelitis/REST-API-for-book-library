<?php

namespace App\DTO\Book;

use App\Traits\DTO\Book\AuthorIDList;
use App\Traits\DTO\Book\Title;
use App\Traits\DTO\Book\Description;
use App\Traits\DTO\Book\ImageName;
use App\Traits\DTO\Book\PublicationDate;

class DTOCreate
{
    use Title;
    use Description;
    use ImageName;
    use PublicationDate;
    use AuthorIDList;
}