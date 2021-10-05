<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use OhKannaDuh\Repositories\Behaviours\HasRepository;

final class BadSpy extends Model
{
    use HasRepository;
}
