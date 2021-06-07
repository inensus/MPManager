<?php


namespace Tests\Feature;


use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_list_registered_users(): void
    {
        UserFactory::times(30)->create();
        $user = User::query()->first();

        $request = $this->get('/api/admin/users', [
            'Authorization' => "Bearer {$this->generateJWTTokenForUser($user)}"
        ]);
        $request->assertStatus(200);
        self::assertEquals(30, $request->json()['total']);
    }

    public function test_create_user(): void
    {
        $user = UserFactory::new()->create();

        $this->withoutExceptionHandling();
        $request = $this->post('/api/admin', [
            'name' => 'Kemal',
            'email' => 'ako@inensus.com',
            'password' => '1234123123',
        ], [
            'Authorization' => "Bearer {$this->generateJWTTokenForUser($user)}"
        ]);
        $request->assertStatus(201);
        $user = User::query()->latest()->first();
        echo "user {$user->name}";
        self::assertTrue(Hash::check('1234123123', $user->password));
        self::assertEquals('ako@inensus.com', $user->email);
    }

    public function test_update_user_name(): void
    {
        $this->withoutExceptionHandling();
        $user = UserFactory::new()->create();
        $request = $this->put("/api/admin/{$user->id}", ['name' => 'Ali'], [
            'Authorization' => "Bearer {$this->generateJWTTokenForUser($user)}"
        ]);
        $request->assertStatus(200);
        $user = User::query()->first();
        self::assertEquals('Ali', $user->name);
    }

    public function test_update_user_password(): void
    {
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

    public function test_reset_user_password(): void
    {
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

    public function test_reset_password_with_non_existing_email(): void
    {
        $request = $this->post('/api/admin/forgot-password', ['email' => 'ako@inensus.com']);
        $request->assertStatus(422);
    }

    private function generateJWTTokenForUser(User|Model $user): string
    {
        return JWTAuth::fromUser($user);
    }
}
