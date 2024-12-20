<?php

namespace CeremonyCrmMod\Core\Billing;

use CeremonyCrmMod\Core\Settings\Models\Permission;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(Models\BillingAccount::class);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

    $this->app->sidebar->addLink(1, 30100, 'billing', $this->translate('Billing'), 'fas fa-file-invoice-dollar', str_starts_with($this->app->requestedUri, 'billing'));

    // if (str_starts_with($this->app->requestedUri, 'billing')) {
    //   $this->app->sidebar->addHeading1(2, 30100, $this->translate('Billing'));
    //   $this->app->sidebar->addLink(2, 30200, 'billing', $this->translate('Billing Accounts'), 'fas fa-file-invoice-dollar');
    // }
  }

  public function installTables() {
    $mBillingAccount = new \CeremonyCrmMod\Core\Billing\Models\BillingAccount($this->app);
    $mBillingAccountService = new \CeremonyCrmMod\Core\Billing\Models\BillingAccountService($this->app);

    $mBillingAccount->dropTableIfExists()->install();
    $mBillingAccountService->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
  
    $mPermission = new \CeremonyCrmMod\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Core/Billing/Models/BillingAccount:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Billing/Models/BillingAccountService:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Billing/Controllers/BillingAccount",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}