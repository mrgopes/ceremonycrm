<?php

namespace CeremonyCrmMod\Sales\Leads\Models;

use CeremonyCrmMod\Core\Customers\Models\Activity;

class LeadActivity extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'lead_activities';
  public string $eloquentClass = Eloquent\LeadActivity::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'ACTIVITY' => [ self::BELONGS_TO, Activity::class, 'id_activity', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Lead',
        'model' => 'CeremonyCrmMod/Sales/Leads/Models/Lead',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_activity' => [
        'type' => 'lookup',
        'title' => 'Activity',
        'model' => 'CeremonyCrmMod/Core/Customers/Models/Activity',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }
}