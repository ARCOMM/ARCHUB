<?php

namespace App;

use Exception;

enum RoleEnum
{
    case RECRUIT;
    case MEMBER;
    case RETIRED;
    case TESTER;
    case SENIOR_TESTER;
    case OPERATIONS;
    case RECRUITER;
    case STAFF;
    case ADMIN;

    public function id(): string
    {
        return match ($this) {
            RoleEnum::RECRUIT => config('services.discord.recruit_role'),
            RoleEnum::MEMBER => config('services.discord.member_role'),
            RoleEnum::RETIRED => config('services.discord.retired_role'),
            RoleEnum::TESTER => config('services.discord.tester_role'),
            RoleEnum::SENIOR_TESTER => config('services.discord.senior_tester_role'),
            RoleEnum::OPERATIONS => config('services.discord.operations_role'),
            RoleEnum::RECRUITER => config('services.discord.recruiter_role'),
            RoleEnum::STAFF => config('services.discord.staff_role'),
            RoleEnum::ADMIN => config('services.discord.admin_role'),
            default => throw new Exception("RoleId not found"),
        };
    }
}
