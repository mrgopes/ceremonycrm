<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Settings\Models\Tag;

use \ADIOS\Core\Db\Column\Lookup;

class CompanyTag extends \HubletoMain\Core\Model
{
  public string $table = 'company_tags';
  public string $eloquentClass = Eloquent\CompanyTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class, 'CASCADE'))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class, 'CASCADE'))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Company Categories';
    $description->ui['addButtonText'] = 'Add Company';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
