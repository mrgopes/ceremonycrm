<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Person;
use CeremonyCrmApp\Modules\Core\Settings\Models\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\DealStatus;
use CeremonyCrmApp\Modules\Core\Settings\Models\Pipeline;
use CeremonyCrmApp\Modules\Core\Settings\Models\PipelineStep;
use CeremonyCrmApp\Modules\Core\Settings\Models\Setting;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;
use CeremonyCrmApp\Modules\Sales\Deals\Models\DealHistory;
use CeremonyCrmApp\Modules\Sales\Deals\Models\DealLabel;
use CeremonyCrmApp\Modules\Sales\Leads\Models\Lead;
use Exception;

class Deal extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deals';
  public string $eloquentClass = Eloquent\Deal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id'],
    'PERSON' => [ self::HAS_ONE, Person::class, 'id', 'id_person'],
    'PIPELINE' => [ self::HAS_ONE, Pipeline::class, 'id', 'id_pipeline'],
    'STATUS' => [ self::HAS_ONE, DealStatus::class, 'id', 'id_deal_status'],
    'PIPELINE_STEP' => [ self::HAS_ONE, PipelineStep::class, 'id', 'id_pipeline_step'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'HISTORY' => [ self::HAS_MANY, DealHistory::class, 'id_deal', 'id'],
    'LABELS' => [ self::HAS_MANY, DealLabel::class, 'id_deal', 'id' ],
    'SERVICES' => [ self::HAS_MANY, DealService::class, 'id_deal', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, DealActivity::class, 'id_deal', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, DealDocument::class, 'id_deal', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [

      'title' => [
        'type' => 'varchar',
        'title' => 'Title',
        'required' => true,
      ],
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'id_person' => [
        'type' => 'lookup',
        'title' => 'Contact Person',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Person',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Origin Lead',
        'model' => 'CeremonyCrmApp/Modules/Sales/Leads/Models/Lead',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
        'readonly' => true,
      ],
      'price' => [
        'type' => 'float',
        'title' => 'Price',
        'required' => true,
      ],
      'id_currency' => [
        'type' => 'lookup',
        'title' => 'Currency',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Currency',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'date_expected_close' => [
        'type' => 'date',
        'title' => 'Expected Close Date',
        'required' => false,
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Assigned User',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
        'foreignKeyOnUpdate' => 'RESTRICT',
        'foreignKeyOnDelete' => 'RESTRICT',
        'required' => true,
      ],
      'date_created' => [
        'type' => 'date',
        'title' => 'Date Created',
        'required' => true,
        'readonly' => true,
      ],
      'id_pipeline' => [
        'type' => 'lookup',
        'title' => 'Pipeline',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
      'id_pipeline_step' => [
        'type' => 'lookup',
        'title' => 'Pipeline Step',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
      'note' => [
        'type' => 'text',
        'title' => 'Notes',
        'required' => false,
      ],
      'source_channel' => [
        'type' => 'varchar',
        'title' => 'Source Channel',
        'required' => false,
      ],
      'is_archived' => [
        'type' => 'boolean',
        'title' => 'Archived',
        'required' => false,
      ],
      'id_deal_status' => [
        'type' => 'lookup',
        'title' => 'Status',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/DealStatus',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Deals';
    $description['ui']['addButtonText'] = 'Add Deal';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    $description['columns']['labels'] = ["title" => "Labels"];
    unset($description['columns']['note']);
    unset($description['columns']['id_person']);
    unset($description['columns']['source_channel']);
    unset($description['columns']['is_archived']);
    unset($description['columns']['id_lead']);
    unset($description['columns']['id_pipeline']);
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $mSettings = new Setting($this->app);
    $defaultPipeline =(int) $mSettings->eloquent
      ->where("key", "Modules\Core\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->value
    ;

    $description = parent::formDescribe();
    $description['defaultValues']['is_archived'] = 0;
    $description['defaultValues']['id_deal_status'] = 1;
    $description['defaultValues']['date_created'] = date("Y-m-d");
    $description['defaultValues']['id_pipeline'] = $defaultPipeline;
    $description['defaultValues']['id_pipeline_step'] = null;
    $description['includeRelations'] = [
      'COMPANY',
      'USER',
      'STATUS',
      'PERSON',
      'PIPELINE',
      'PIPELINE_STEP',
      'CURRENCY',
      'HISTORY',
      'LABELS',
      'LEAD',
      'SERVICES',
      'ACTIVITIES',
      'DOCUMENTS',
    ];
    return $description;
  }

  public function onAfterCreate(array $record, $returnValue)
  {
    $mDealHistory = new DealHistory($this->app);
    $mDealHistory->eloquent->create([
      "change_date" => date("Y-m-d"),
      "id_deal" => $record["id"],
      "description" => "Deal created"
    ]);

    return $this->app->dispatchEventToPlugins("onModelAfterCreate", [
      "model" => $this,
      "data" => $record,
      "returnValue" => $returnValue,
    ])["returnValue"];
  }

  public function getOwnership($record) {
    if ($record["id_company"] && !isset($record["checkOwnership"])) {
      $mCompany = new Company($this->app);
      $company = $mCompany->eloquent
        ->where("id", $record["id_company"])
        ->first()
      ;

      if ($company->id_user != $record["id_user"]) {
        throw new Exception("This deal cannot be assigned to the selected user,\nbecause they are not assigned to the selected company.
        ");
      }
    }
  }

  public function onBeforeCreate(array $record): array
  {
    $this->getOwnership($record);
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $this->getOwnership($record);

    $deal = $this->eloquent->find($record["id"])->toArray();
    $mDealHistory = new DealHistory($this->app);

    if ((float) $deal["price"] != (float) $record["price"]) {
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $record["id"],
        "description" => "Price changed to ".$record["price"]." ".$record["CURRENCY"]["code"]
      ]);
    }
    if ((string) $deal["date_expected_close"] != (string) $record["date_expected_close"]) {
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $record["id"],
        "description" => "Expected Close Date changed to ".date("d.m.Y", strtotime((string) $record["date_expected_close"]))
      ]);
    }

    return $record;
  }
}