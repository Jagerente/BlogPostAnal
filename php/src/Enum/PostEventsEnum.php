<?php

namespace App\Enum;

enum PostEventsEnum: string
{
    case Created = "EVENT_POST_CREATED";
    case Edited = "EVENT_POST_EDITED";
    case Deleted = "EVENT_POST_DELETED";
    case Moderated = "EVENT_POST_MODERATED";
}