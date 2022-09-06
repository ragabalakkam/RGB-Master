<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

# models
use App\Models\User;
use App\Models\Locations\Country;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $country = Country::find(2);
        $state = random($country->states);
        $city = random($state->cities);
        $district = random($city->districts);
        $address = [$country->id, $state->id, $city->id, $district->id, $this->faker->streetName, $this->faker->buildingNumber, $this->faker->postcode];
        
        return [
            'name'              => $this->faker->name,
            'username'          => $this->faker->unique()->userName,
            'email'             => normalize_email($this->faker->unique()->safeEmail),
            'email_verified_at' => $this->faker->numberBetween(0, 1) ?  now() : null,
            'phone'             => $this->faker->unique()->numerify("9665########"),
            'phone_verified_at' => $this->faker->numberBetween(0, 1) ?  now() : null,
            'password'          => Hash::make("passw&rd"),
            'created_at'        => $this->faker->dateTime(),
            'updated_at'        => $this->faker->dateTime(),
            'address'           =>  $address,
            'full_address'      =>  getFullAddress($address),
        ];
    }
}
