<?php

use App\Models\Configurations\Group;
use App\Models\Configurations\ConfigurationGroup;
use App\Models\Configuration;

function create_configruation_if_not_exists($group_key, $datatype, $key, $value)
{
  if (is_null(getConfig($key)))
  {
    $group = Group::firstOrCreate(['key' => $group_key]);

    $config = Configuration::create([
      'datatype'  => $datatype,
      'key'       => $key,
      'value'     => $value,
    ]);

    ConfigurationGroup::create([
      'configuration_group_id'  => $group->id,
      'configuration_id'        => $config->id,
    ]);
  }

  return getConfig($key);
}
