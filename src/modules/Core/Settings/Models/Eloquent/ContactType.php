<?php

namespace CeremonyCrmMod\Core\Settings\Models\Eloquent;

use CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactType extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'contact_types';

}