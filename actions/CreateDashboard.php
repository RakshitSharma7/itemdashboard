<?php

class CreateDashboard extends CController {

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        $fields = [
            'itemids' => 'string'
        ];

        $ret = $this->validateInput($fields);

        if (!$ret) {
            $this->setResponse(
                new CControllerResponseFatal(_('Invalid input'))
            );
        }

        return $ret;
    }

    protected function doAction(): void {
        // If no input → show form
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

        if (empty($itemids)) {
            throw new Exception('No item IDs provided');
        }

        // Create dashboard
        $dashboard = API::Dashboard()->create([
            'name' => 'Auto Dashboard ' . date('Y-m-d H:i:s'),
            'pages' => [[
                'widgets' => []
            ]]
        ]);

        $dashboardid = $dashboard['dashboardids'][0];

        // Widget positioning
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
                        'value' => $itemid
                    ]
                ]
            ]);

            $x += 6;
            if ($x >= 12) {
                $x = 0;
                $y += 5;
            }
        }

        // Redirect to created dashboard
        $this->setResponse(
            new CControllerResponseRedirect(
                (new CUrl('zabbix.php'))
                    ->setArgument('action', 'dashboard.view')
                    ->setArgument('dashboardid', $dashboardid)
            )
        );
    }
}
