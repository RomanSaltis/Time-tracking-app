<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test user registration.
     *
     * @return void
     */
    public function testUserRegistration(): void
    {
        $faker = \Faker\Factory::create();

        $name = $faker->name;
        $email = Str::random(6) . '@testing.com';
        $password = $faker->password(8);

        $response = $this->post('/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
        $this->assertTrue(Hash::check($password, User::where('email', $email)->first()->password));
    }

    /**
     * Test user login with incorrect email.
     *
     * @return void
     */
    public function testUserLoginWithIncorrectEmail(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'incorrect@testing.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['message' => 'Invalid credentials']);
        $this->assertGuest();
    }

    /**
     * Test user login.
     *
     * @return void
     */
    public function testUserLogin(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/tasks');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testUserLogout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserIndex(): void
    {
        $user = User::factory()
            ->create(['name' => 'Test User', 'email' => Str::random(6) . '@testing.com']);
        $this->actingAs($user);

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    /**
     * Test user update.
     *
     * @return void
     */
    public function testUserUpdate(): void
    {
        $user = User::factory()->create();

        $newName = 'John Doe';
        $newEmail = 'john.doe@example.com';
        $newPassword = 'newpassword';

        $response = $this->actingAs($user)
            ->put('/users/' . $user->id, [
                'name' => $newName,
                'email' => $newEmail,
                'password' => $newPassword,
            ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $newName,
            'email' => $newEmail,
        ]);
    }

    /**
     * Test user deletion.
     *
     * @return void
     */
    public function testUserDeletion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->delete(route('users.destroy', $user->id));

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
