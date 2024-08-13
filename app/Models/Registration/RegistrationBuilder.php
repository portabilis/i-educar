<?php

namespace App\Models\Registration;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Registration>
 */
class RegistrationBuilder extends Builder
{
    use RegistrationScopes;
}
