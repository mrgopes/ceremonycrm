<?php

namespace CeremonyCrmMod\Sales\Leads\Models\Eloquent;

use CeremonyCrmMod\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmMod\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\LeadStatus;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
use CeremonyCrmMod\Sales\Deals\Models\Eloquent\Deal;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\LeadHistory;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\LeadLabel;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'leads';

  public function DEAL(): HasOne {
    return $this->hasOne(Deal::class, 'id_lead', 'id' );
  }
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id' );
  }
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user','id' );
  }
  public function PERSON(): HasOne {
    return $this->hasOne(Person::class, 'id', 'id_person');
  }
  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
  public function STATUS(): HasOne {
    return $this->hasOne(LeadStatus::class, 'id', 'id_lead_status');
  }
  public function HISTORY(): HasMany {
    return $this->hasMany(LeadHistory::class, 'id_lead', 'id');
  }
  public function LABELS(): HasMany {
    return $this->hasMany(LeadLabel::class, 'id_lead', 'id');
  }
  public function SERVICES(): HasMany {
    return $this->hasMany(LeadService::class, 'id_lead', 'id');
  }
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(LeadActivity::class, 'id_lead', 'id' );
  }
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(LeadDocument::class, 'id_lead', 'id' );
  }
}
