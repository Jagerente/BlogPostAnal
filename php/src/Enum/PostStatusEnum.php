<?php

namespace App\Enum;

enum PostStatusEnum: string
{
    public const Pending = 'pending';
    public const Declined = 'declined';
    public const Approved = 'approved';
}