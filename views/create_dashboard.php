<?php

$form = (new CForm())
    ->setAttribute('method', 'post')
    ->setAttribute('action', 'zabbix.php?action=itemdashboard.create');

$form->addItem(
    new CFormGrid([
        [
            new CLabel(_('Item IDs (comma separated)'), 'itemids'),
            new CTextBox('itemids', '', false, 255)
        ]
    ])
);

$form->addItem(
    new CSubmit('submit', _('Create Dashboard'))
);

(new CWidget())
    ->setTitle(_('Item Dashboard Creator'))
    ->addItem($form)
    ->show();
