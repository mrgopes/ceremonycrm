<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends \ADIOS\Core\Model\Eloquent {
  public $table = 'persons';

  // public function REPORT(): BelongsTo
  // {
  //   return $this->belongsTo(\EMonitorApp\Models\Eloquent\Report::class, 'id_report', 'id')->orderBy('name', 'asc');
  // }

}