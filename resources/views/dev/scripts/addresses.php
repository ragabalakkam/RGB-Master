<?php

use App\Models\User;
use App\Models\Clients\Client;

use App\Models\Locations\Country;

function generate_random_address($separators = ['en' => " - ", 'ar' => " - "], $order = ['country', 'state', 'city', 'district', 'street', 'building_number', 'postal_code'])
{
  $faker = Faker\Factory::create();

  $country = Country::find(2);
  $state = random($country->states);
  $city = random($state->cities);
  $district = random($city->districts);

  $address = [
    $country->id,
    $state->id,
    $city->id,
    $district->id,
    $faker->streetName,
    $faker->buildingNumber,
    $faker->postcode
  ];

  return array_merge(getFullAddress($address, $separators, $order), ['arr' => $address]);
}

function update_full_address($model)
{
  return $model->update(['full_address' => getFullAddress($model->address)]);
}

function update_all_addresses()
{
  $values = [
    User::all(),
    Client::all(),
  ];

  foreach ($values as $model_values)
  {
    foreach ($model_values as $model) {
      update_full_address($model);
    }
  }
}

