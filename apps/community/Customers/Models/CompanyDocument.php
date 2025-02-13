<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Documents\Models\Document;

use \ADIOS\Core\Db\Column\Lookup;

class CompanyDocument extends \HubletoMain\Core\Model
{
  public string $table = 'company_documents';
  public string $eloquentClass = Eloquent\CompanyDocument::class;

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class, 'CASCADE'))->setRequired(),
    ]);
  }

  public function describeInput(string $columnName): \ADIOS\Core\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'hyperlink':
        $description->setReactComponent('InputHyperlink');
      break;
    }
    return $description;
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    if ($this->main->urlParamAsInteger('idCompany') > 0) {
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
