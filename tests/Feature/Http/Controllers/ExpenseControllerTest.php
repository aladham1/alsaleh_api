<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ExpenseController
 */
class ExpenseControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $expenses = Expense::factory()->count(3)->create();

        $response = $this->get(route('expense.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_saves(): void
    {
        $project = Project::factory()->create();
        $total = $this->faker->randomFloat(0, 0, 500);
        $paid_at = now();

        $response = $this->post(route('expense.store'), [
            'project_id' => $project->id,
            'total' => $total,
            'paid_at' => $paid_at,
        ]);

        $expenses = Expense::query()
            ->where('project_id', $project->id)
            ->where('total', $total)
            ->where('paid_at', $paid_at)
            ->get();
        $this->assertCount(1, $expenses);
        $expense = $expenses->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $expense = Expense::factory()->create();

        $response = $this->get(route('expense.show', $expense));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }




    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $expense = Expense::factory()->create();
        $project = Project::factory()->create();
        $total = $this->faker->randomFloat(0, 0, 500);
        $paid_at = now();

        $response = $this->put(route('expense.update', $expense), [
            'project_id' => $project->id,
            'total' => $total,
            'paid_at' => $paid_at,
        ]);

        $expense->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($project->id, $expense->project_id);
        $this->assertEquals($total, $expense->total);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $expense = Expense::factory()->create();

        $response = $this->delete(route('expense.destroy', $expense));

        $response->assertNoContent();

        $this->assertModelMissing($expense);
    }
}
