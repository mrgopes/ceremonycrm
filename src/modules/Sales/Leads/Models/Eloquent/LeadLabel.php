<?php

namespace CeremonyCrmMod\Sales\Leads\Models\Eloquent;

use CeremonyCrmMod\Core\Settings\Models\Eloquent\Label;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadLabel extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_labels';

  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
  public function LABEL(): BelongsTo {
    return $this->belongsTo(Label::class, 'id_label', 'id');
  }
}
