<?php


namespace Tests\Feature;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserResource extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function listRegisteredUsers(): void
    {
        //create random users
        $users = factory(User::class)->times(30)->create();

        $request = $this->get('/api/admin/users');
        $request->assertStatus(200);

        $this->assertEquals($request->json()['total'], 30);
    }

    /** @test */
    public function createUser(): void
    {
        $this->withoutExceptionHandling();
        $request = $this->post('/api/admin', [
            'name' => 'Kemal',
            'email' => 'ako@inensus.com',
            'password' => '1234123123',
        ]);

        $request->assertStatus(201);

        $user = User::first();

        $this->assertTrue(Hash::check('1234123123', $user->password));

        $this->assertEquals($user->email, 'ako@inensus.com');
    }

    /** @test */
    public function updateUserWithoutPassword(): void
    {
        $this->withoutExceptionHandling();
        //create user
        $request = $this->post('/api/admin', [
            'name' => 'Kemal',
            'email' => 'ako@inensus.com',
            'password' => '1234123123',
        ]);

        $user = User::first();

        $request = $this->put('/api/admin/' . $user->id, ['name' => 'Ali']);

        $request->assertStatus(200);
        $user = User::first();

        $this->assertEquals($user->name, 'Ali');
    }

    /** @test */
    public function updateUserPassword(): void
    {
        //create user
        $this->post('/api/admin', [
            'name' => 'Kemal',
            'email' => 'ako@inensus.com',
            'password' => '1234123123',
        ]);

        $user = User::first();

        $request = $this->put('/api/admin/' . $user->id, ['password' => 'password']);

        $request->assertStatus(200);
        $user = User::first();

        $this->assertTrue(Hash::check('password', $user->password));
    }

    /** @test */
    public function resetUserPassword(): void
    {
        //create user
        $this->post('/api/admin', [
            'name' => 'Kemal',
            'email' => 'ako@inensus.com',
            'password' => '1234123123',
        ]);

        $user = User::first();

        $request = $this->post('/api/admin/forgot-password', ['email' => $user->email]);

        $request->assertStatus(200);

        $userWitNewPassword = User::first();
        $this->assertNotEquals($user->password, $userWitNewPassword->password);
    }

    /** @test */
    public function resetPasswordWithNonExistingEmail(): void
    {
        $request = $this->post('/api/admin/forgot-password', ['email' => 'ako@inensus.com']);

        $request->assertStatus(422);

    }
}
