<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'User 1',
            'email' => 'user1@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now()->startOfDay(),
        ]);
    }

    public function test_user_can_login()
    {
        $this->post(route('user.login'), [
            'email' => $this->user->email,
            'password' => 'password'
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token',
                'token_type'
            ])
        ;
    }

    public function credentialsProvider(): array
    {
        return [
            [null, 'password'],
            [1234, 'password'],
            [null, null],
            ['wrong@gmail.com', 'password'],
            ['user1@gmail.com', null],
            ['user1@gmail.com', 'wrong_pass'],
        ];
    }

    /**
     * @test
     * @dataProvider credentialsProvider()
     */
    public function test_invalid_credentials($email, $password)
    {
        $this->post(route('user.login'), [
            'email' => $email,
            'password' => $password
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
