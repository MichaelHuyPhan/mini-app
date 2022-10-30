<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
        ]);
    }

    public function test_admin_can_login()
    {
        $this->post(route('admin.login'), [
            'email' => $this->admin->email,
            'password' => 'password'
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token',
                'token_type'
            ])
        ;
    }

    protected function credentialsProvider(): array
    {
        return [
            [null, 'password'],
            [1234, 'password'],
            [null, null],
            ['wrong@gmail.com', 'password'],
            ['admin1@gmail.com', null],
            ['admin1@gmail.com', 'wrong_pass'],
        ];
    }

    /**
     * @test
     * @dataProvider credentialsProvider()
     */
    public function test_invalid_credentials($email, $password)
    {
        $this->post(route('admin.login'), [
            'email' => $email,
            'password' => $password
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
