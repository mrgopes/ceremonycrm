<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class ContactTypes extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'contact-types', 'content' => $this->app->translate('Contact Types') ],
    ]);
  }

 }