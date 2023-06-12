<?php

namespace App\Enum;

enum RoleEnum: string
{
    public const Author = 'ROLE_AUTHOR';
    public const Moderator = 'ROLE_MODERATOR';
    public const User = 'ROLE_USER';
}