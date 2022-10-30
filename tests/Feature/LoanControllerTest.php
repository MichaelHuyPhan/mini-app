<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, [], 'api');
        $this->admin = Admin::factory()->create();
        Sanctum::actingAs($this->admin, [], 'admin');
    }

    public function test_auth_user_can_create_loan()
    {
        $amount = 10000;
        $term = 3;
        $repayment_amount = $amount/$term;
        $this->post(route('loans.create'), [
            'amount' => $amount,
            'term' => $term,
        ])
        ->assertSuccessful();

        $this->assertDatabaseHas('loans', [
            'user_id' => $this->user->id,
            'amount' => $amount,
            'term' => $term,
            'status' => Loan::STATUS_PENDING
        ]);

        $loan = Loan::where('user_id', $this->user->id)->first();

        $this->assertDatabaseCount('scheduled_repayments',$term);
        $this->assertDatabaseHas('scheduled_repayments', [
            'loan_id' => $loan->id,
            'repayment_date' => Carbon::now()->startOfDay()->addDay(7),
            'amount' => $repayment_amount,
            'status' => ScheduledRepayment::STATUS_PENDING
        ]);
    }

    public function test_auth_user_can_view_his_loan()
    {
        $amount = 10000;
        $term = 3;
        $this->post(route('loans.create'), [
            'amount' => $amount,
            'term' => $term,
        ])
            ->assertSuccessful();

        $this->get(route('loans.view'))
        ->assertSuccessful()
        ->assertJsonStructure([
            'loans' => [
                '*' => [
                    'amount',
                    'term',
                    'status',
                    'scheduled_repayments' => [
                        '*' => [
                            'repayment_date',
                            'amount',
                            'status',
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function test_auth_user_can_repay()
    {
        $this->test_auth_user_can_create_loan();
        $loan = Loan::where('user_id', $this->user->id)->first();

        $this->put(route('admin.loans.approve', $loan->id));

        $this->assertDatabaseMissing('scheduled_repayments', [
            'loan_id' => $loan->id,
            'status' => ScheduledRepayment::STATUS_PAID
        ]);

        $this->put(route('loans.repay'),[
            'loan_id' => $loan->id,
            'amount' => 10000,
        ])->assertSuccessful();

        $this->assertDatabaseHas('scheduled_repayments', [
            'loan_id' => $loan->id,
            'status' => ScheduledRepayment::STATUS_PAID
        ]);
        $this->assertDatabaseHas('loans', [
            'user_id' => $this->user->id,
            'status' => Loan::STATUS_APPROVED
        ]);

        $this->put(route('loans.repay'),[
            'loan_id' => $loan->id,
            'amount' => 10000,
        ])->assertSuccessful();

        $this->put(route('loans.repay'),[
            'loan_id' => $loan->id,
            'amount' => 10000,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('scheduled_repayments', [
            'loan_id' => $loan->id,
            'status' => ScheduledRepayment::STATUS_PENDING
        ]);
        $this->assertDatabaseHas('loans', [
            'user_id' => $this->user->id,
            'status' => Loan::STATUS_PAID
        ]);
    }
}
