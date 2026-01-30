<?php
declare(strict_types=1);

namespace Modules\Itemdashboard\Actions;

use API;
use CController;
use CControllerResponseData;
use CControllerResponseRedirect;
use CUrl;

class CreateDashboard extends CController {

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    protected function doAction(): void {

        // Show form first
        if (!$this->hasInput('itemids')) {
            $this->setResponse(
                new CControllerResponseData([
                    'title' => _('Create Item Dashboard')
                ])
            );
            return;
        }

        // Parse item IDs
        $itemids = array_filter(
            array_map('trim', explode(',', $this->getInput('itemids')))
        );

        if (!$itemids) {
            throw new \Exception('No item IDs provided');
        }

        // Create dashboard
        $dashboard = API::Dashboard()->create([
            'name' => 'Auto Dashboard ' . date('Y-m-d H:i:s'),
            'pages' => [[
                'widgets' => []
            ]]
        ]);

        $dashboardid = $dashboard['dashboardids'][0];

        $x = 0;
        $y = 0;

        foreach ($itemids as $itemid) {
            API::DashboardWidget()->create([
                'dashboardid' => $dashboardid,
                'type' => 'graph',
                'x' => $x,
                'y' => $y,
                'width' => 6,
                'height' => 5,
                'fields' => [
                    [
                        'type'  => ZBX_WIDGET_FIELD_TYPE_ITEM,
                        'name'  => 'itemid',
                        'value' => (string) $itemid
                    ]
                ]
            ]);

            $x += 6;
            if ($x >= 12) {
                $x = 0;
                $y += 5;
            }
        }

        // Redirect to dashboard
        $this->setResponse(
            new CControllerResponseRedirect(
                (new CUrl('zabbix.php'))
                    ->setArgument('action', 'dashboard.view')
                    ->setArgument('dashboardid', $dashboardid)
            )
        );
    }
}
