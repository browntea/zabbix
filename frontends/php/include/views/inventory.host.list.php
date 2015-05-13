<?php
/*
** Zabbix
** Copyright (C) 2001-2015 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

$hostInventoryWidget = (new CWidget())->setTitle(_('Host inventory'));

$rForm = new CForm('get');
$rForm->addItem((new CList())->addItem(array(_('Group').SPACE, $this->data['pageFilter']->getGroupsCB())));
$hostInventoryWidget->setControls($rForm);

// filter
$filterForm = new CFilter('web.hostinventories.filter.state');

$filterColumn = new CFormList();

// getting inventory fields to make a drop down
$inventoryFields = getHostInventories(true); // 'true' means list should be ordered by title
$inventoryFieldsComboBox = new CComboBox('filter_field', $this->data['filterField']);
foreach ($inventoryFields as $inventoryField) {
	$inventoryFieldsComboBox->addItem(
		$inventoryField['db_field'],
		$inventoryField['title']
	);
}

$filterColumn->addRow(
		_('Field'),
		array(
			$inventoryFieldsComboBox,
			new CComboBox('filter_exact', $this->data['filterExact'], null, array(
				0 => _('like'),
				1 => _('exactly')
			)),
			new CTextBox('filter_field_value', $this->data['filterFieldValue'], 20)
		)
);

$filterForm->addColumn($filterColumn);
$hostInventoryWidget->addItem($filterForm);

$table = new CTableInfo();
$table->setHeader(array(
	make_sorting_header(_('Host'), 'name', $this->data['sort'], $this->data['sortorder']),
	_('Group'),
	make_sorting_header(_('Name'), 'pr_name', $this->data['sort'], $this->data['sortorder']),
	make_sorting_header(_('Type'), 'pr_type', $this->data['sort'], $this->data['sortorder']),
	make_sorting_header(_('OS'), 'pr_os', $this->data['sort'], $this->data['sortorder']),
	make_sorting_header(_('Serial number A'), 'pr_serialno_a', $this->data['sort'], $this->data['sortorder']),
	make_sorting_header(_('Tag'), 'pr_tag', $this->data['sort'], $this->data['sortorder']),
	make_sorting_header(_('MAC address A'), 'pr_macaddress_a', $this->data['sort'], $this->data['sortorder'])
));

foreach ($this->data['hosts'] as $host) {
	$hostGroups = array();
	foreach ($host['groups'] as $group) {
		$hostGroups[] = $group['name'];
	}
	natsort($hostGroups);
	$hostGroups = implode(', ', $hostGroups);

	$row = array(
		new CLink(
			$host['name'],
			'?hostid='.$host['hostid'].url_param('groupid'),
			($host['status'] == HOST_STATUS_NOT_MONITORED) ? 'not-monitored' : ''
		),
		$hostGroups,
		zbx_str2links($host['inventory']['name']),
		zbx_str2links($host['inventory']['type']),
		zbx_str2links($host['inventory']['os']),
		zbx_str2links($host['inventory']['serialno_a']),
		zbx_str2links($host['inventory']['tag']),
		zbx_str2links($host['inventory']['macaddress_a'])
	);

	$table->addRow($row);
}

$table = array($table, $this->data['paging']);
$hostInventoryWidget->addItem($table);

return $hostInventoryWidget;
