<?php
declare(strict_types=1);

namespace Modules\Itemdashboard;

use CModule;

class Module extends CModule {

    public function init(): void {
        $this->addMenuItem([
            'label' => _('Item Dashboard'),
            'url'   => 'zabbix.php?action=itemdashboard.create',
            'icon'  => 'icon-dashboard'
        ]);
    }
}
