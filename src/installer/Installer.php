<?php

namespace CeremonyCrmApp\Installer;

use CeremonyCrmMod\Core\Settings\Models\ {
  Permission, Profile, RolePermission, User, UserRole, UserHasRole
};

class Installer {
  public \CeremonyCrmApp $app;
  public string $adminName = '';
  public string $adminFamilyName = '';
  public string $adminEmail = '';
  public string $adminPassword = '';
  public string $companyName = '';
  public string $accountRootRewriteBase = '';
  public string $accountRootFolder = '';
  public string $accountRootUrl = '';
  public string $appRootFolder = '';
  public string $appRootUrl = '';
  public string $extRootFolder = '';

  public string $env = '';
  public string $uid = '';
  public string $dbHost = '';
  public string $dbName = '';
  public string $dbUser = '';
  public string $dbPassword = '';

  public bool $randomize = false;

  public array $enabledModules = [];

  public function __construct(
    \CeremonyCrmApp $app,
    string $env,
    string $uid,
    string $companyName,
    string $adminName,
    string $adminFamilyName,
    string $adminEmail,
    string $adminPassword,
    string $accountRootRewriteBase,
    string $accountRootFolder,
    string $accountRootUrl,
    string $appRootFolder,
    string $appRootUrl,
    string $extRootFolder,
    string $dbHost,
    string $dbName,
    string $dbUser,
    string $dbPassword,
    bool $randomize = false
  )
  {
    $this->app = $app;
    $this->env = $env;
    $this->uid = $uid;
    $this->companyName = $companyName;
    $this->adminName = $adminName;
    $this->adminFamilyName = $adminFamilyName;
    $this->adminEmail = $adminEmail;
    $this->adminPassword = $adminPassword;
    $this->accountRootRewriteBase = $accountRootRewriteBase;
    $this->accountRootFolder = $accountRootFolder;
    $this->accountRootUrl = $accountRootUrl;
    $this->appRootFolder = $appRootFolder;
    $this->appRootUrl = $appRootUrl;
    $this->extRootFolder = $extRootFolder;

    $this->dbHost = $dbHost;
    $this->dbName = $dbName;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;

    $this->randomize = $randomize;

  }

  public function validate()
  {
    if (strlen($this->uid) > 32) {
      throw new \CeremonyCrmApp\Exceptions\AccountValidationFailed('Account name is too long.');
    }

    if (!filter_var($this->adminEmail, FILTER_VALIDATE_EMAIL)) {
      throw new \CeremonyCrmApp\Exceptions\AccountValidationFailed('Invalid admin email.');
    }

    if (
      is_file($this->accountRootFolder . '/' . $this->uid)
      || is_dir($this->accountRootFolder . '/' . $this->uid)
    ) {
      throw new \CeremonyCrmApp\Exceptions\AccountAlreadyExists('Account folder already exists');
    }
  }

  public function createDatabase()
  {

    $this->app->pdo->execute("drop database if exists `{$this->dbName}`");
    $this->app->pdo->execute("create database `{$this->dbName}` character set utf8 collate utf8_general_ci");

    $this->app->config['db_name'] = $this->dbName;
    $this->app->config['db_codepage'] = "utf8mb4";
    $this->app->initDatabaseConnections();

    foreach ($this->app->registeredModels as $modelClass) {
      $model = $this->app->getModel($modelClass);
      $this->app->db->addTable(
        $model->getFullTableSqlName(),
        $model->columns(),
        $model->isJunctionTable
      );
    }

  }

  public function installTables()
  {

    (new \CeremonyCrmMod\Core\Settings\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Core\Documents\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Core\Services\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Core\Customers\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Core\Invoices\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Core\Billing\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Sales\Core\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Sales\Leads\Loader($this->app))->installTables();
    (new \CeremonyCrmMod\Sales\Deals\Loader($this->app))->installTables();

    $mProfile = new \CeremonyCrmMod\Core\Settings\Models\Profile($this->app);
    $mUser = new \CeremonyCrmMod\Core\Settings\Models\User($this->app);
    $mUserRole = new \CeremonyCrmMod\Core\Settings\Models\UserRole($this->app);
    $mUserHasRole = new \CeremonyCrmMod\Core\Settings\Models\UserHasRole($this->app);

    $idProfile = $mProfile->eloquent->create(['company' => $this->companyName])->id;

    $idUserAdministrator = $mUser->eloquent->create([
      'login' => $this->adminEmail,
      'password' => $mUser->hashPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => 1,
      'id_active_profile' => $idProfile,
    ])->id;

    $idRoleAdministrator = $mUserRole->eloquent->create(['id' => UserRole::ROLE_ADMINISTRATOR, 'role' => 'Administrator', 'grant_all' => 1])->id;
    $idRoleSalesManager = $mUserRole->eloquent->create(['id' => UserRole::ROLE_SALES_MANAGER, 'role' => 'Sales manager', 'grant_all' => 0])->id;
    $idRoleAccountant = $mUserRole->eloquent->create(['id' => UserRole::ROLE_ACCOUNTANT, 'role' => 'Accountant', 'grant_all' => 0])->id;

    $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;
  }

  public function getConfigAccountContent(): string
  {
    $configAccount = file_get_contents(__DIR__ . '/template/ConfigAccount.tpl');
    $configAccount = str_replace('{{ appDir }}', $this->appRootFolder, $configAccount);
    $configAccount = str_replace('{{ extDir }}', $this->extRootFolder, $configAccount);
    $configAccount = str_replace('{{ appUrl }}', $this->appRootUrl, $configAccount);
    $configAccount = str_replace('{{ dbHost }}', $this->app->config['db_host'], $configAccount);
    $configAccount = str_replace('{{ dbUser }}', $this->dbUser, $configAccount);
    $configAccount = str_replace('{{ dbPassword }}', $this->dbPassword, $configAccount);
    $configAccount = str_replace('{{ dbName }}', $this->dbName, $configAccount);
    $configAccount = str_replace('{{ rewriteBase }}', $this->accountRootRewriteBase . (empty($this->uid) ? '' : $this->uid . '/'), $configAccount);
    $configAccount = str_replace('{{ accountDir }}', $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid), $configAccount);
    $configAccount = str_replace('{{ accountUrl }}', $this->accountRootUrl . (empty($this->uid) ? '' : '/' . $this->uid), $configAccount);

    $configAccount .= '' . "\n";
    $configAccount .= '$config[\'enabledModules\'] = [' . "\n";
    foreach ($this->enabledModules as $module) {
      $configAccount .= '  ' . $module . ',' . "\n";
    }
    $configAccount .= '];' . "\n";

    $configAccount .= '' . "\n";
    $configAccount .= '$config[\'env\'] = \'' . $this->env . '\';' . "\n";

    return $configAccount;
  }

  public function createFoldersAndFiles()
  {
    // folders
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid));
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/log');
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/tmp');
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/upload');

    // ConfigEnv.php

    file_put_contents($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/ConfigAccount.php', $this->getConfigAccountContent());

    // LoadApp.php
    $loadApp = file_get_contents(__DIR__ . '/template/LoadApp.php');
    $loadApp = str_replace('{{ uid }}', $this->uid, $loadApp);
    $loadApp = str_replace('{{ appDir }}', $this->appRootFolder, $loadApp);
    $loadApp = str_replace('{{ extDir }}', $this->extRootFolder, $loadApp);
    file_put_contents($this->accountRootFolder . '/' . $this->uid . '/LoadApp.php', $loadApp);

    // index.php
    copy(
      __DIR__ . '/template/index.php',
      $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/index.php'
    );

    // .htaccess
    copy(
      __DIR__ . '/template/.htaccess',
      $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/.htaccess'
    );
  }

  public function installDefaultPermissions() {
    $modules = $this->app->getModules();
    array_walk($modules, function($module) {
      $module->installDefaultPermissions();
    });
  }

  // public function createDevelScripts()
  // {
  //   @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/devel');

  //   $tplFolder = __DIR__ . '/template';
  //   $accFolder = $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid);

  //   copy($tplFolder . '/devel/Reinstall.php', $accFolder . '/devel/Reinstall.php');
  // }

  // public function getDatabaseUser(): string {
  //   $dbUser = \ADIOS\Core\Helper::str2url($this->companyName);
  //   $dbUser = str_replace('-', '_', $dbUser);
  //   $dbUser =
  //     'usr_' . $dbUser
  //     . ($this->randomize ? '_' . substr(md5(date('YmdHis').rand(1, 10000)), 1, 5) : '')
  //   ;

  //   return $dbUser;
  // }

}