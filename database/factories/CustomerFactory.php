<?php

namespace Database\Factories;

 
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
 


class CustomerFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => Hash::make('pass'),  
                'cpf' => rand(11111111111, 99999999999),
                'type' => "Client"
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
