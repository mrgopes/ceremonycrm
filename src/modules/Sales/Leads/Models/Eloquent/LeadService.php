<?php

namespace CeremonyCrmMod\Sales\Leads\Models\Eloquent;

use CeremonyCrmMod\Core\Services\Models\Eloquent\Service;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadService extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_services';

  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
  public function SERVICE(): BelongsTo {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }

}
