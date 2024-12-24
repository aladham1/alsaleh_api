<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProjectController
 */
class ProjectControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $projects = Project::factory()->count(3)->create();

        $response = $this->get(route('project.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


   

    /**
     * @test
     */
    public function store_saves(): void
    {
        $this->withoutExceptionHandling();

        $name = $this->faker->name;
        $description = $this->faker->text;
        $avatar = $this->faker->word;
        $total_paid = $this->faker->randomFloat(0, 0, 500);
        $total_requested = $this->faker->randomFloat(0, 0, 500);
        $total_remaining = $this->faker->randomFloat(0, 0, 500);
        $min_donation_fee = $this->faker->randomFloat(0, 0, 500);
        $increment_by = $this->faker->randomFloat(0, 0, 500);
        $bank_name = $this->faker->word;
        $bank_branch = $this->faker->word;
        $bank_iban = $this->faker->word;
        $country = $this->faker->country;
        $city = $this->faker->city;
        $gov = $this->faker->word;
        $lat = $this->faker->latitude;
        $lng = $this->faker->longitude;
        $status = $this->faker->randomElement(["active","archived"]);

        $response = $this->post(route('project.store'), [
            'name' => $name,
            'description' => $description,
            'avatar' => $avatar,
            'total_paid' => $total_paid,
            'total_requested' => $total_requested,
            'total_remaining' => $total_remaining,
            'min_donation_fee' => $min_donation_fee,
            'increment_by' => $increment_by,
            'bank_name' => $bank_name,
            'bank_branch' => $bank_branch,
            'bank_iban' => $bank_iban,
            'country' => $country,
            'city' => $city,
            'gov' => $gov,
            'lat' => $lat,
            'lng' => $lng,
            'status' => $status,
            'images' => ['/tmp/path'],
            'videos' => ['/tmp/path']
        ]);

        $projects = Project::query()
            ->where('name', $name)
            ->where('description', $description)
            ->where('avatar', $avatar)
            ->where('total_paid', $total_paid)
            ->where('total_requested', $total_requested)
            ->where('total_remaining', $total_remaining)
            ->where('min_donation_fee', $min_donation_fee)
            ->where('increment_by', $increment_by)
            ->where('bank_name', $bank_name)
            ->where('bank_branch', $bank_branch)
            ->where('bank_iban', $bank_iban)
            ->where('country', $country)
            ->where('city', $city)
            ->where('gov', $gov)
            ->where('lat', $lat)
            ->where('lng', $lng)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $projects);

        $this->assertCount(1 , $projects->first()->media()->where('type' , 'image')->get());
        $this->assertCount(1 , $projects->first()->media()->where('type' , 'video')->get());

        $response->assertCreated();
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('project.show', $project));

        $response->assertOk();
    }



    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $project = Project::factory()->create();
        $name = $this->faker->name;
        $description = $this->faker->text;
        $avatar = $this->faker->word;
        $total_paid = $this->faker->randomFloat(0, 0, 500);
        $total_requested = $this->faker->randomFloat(0, 0, 500);
        $total_remaining = $this->faker->randomFloat(0, 0, 500);
        $min_donation_fee = $this->faker->randomFloat(0, 0, 500);
        $increment_by = $this->faker->randomFloat(0, 0, 500);
        $bank_name = $this->faker->word;
        $bank_branch = $this->faker->word;
        $bank_iban = $this->faker->word;
        $country = $this->faker->country;
        $city = $this->faker->city;
        $gov = $this->faker->word;
        $lat = $this->faker->latitude;
        $lng = $this->faker->longitude;
        $status = $this->faker->randomElement(["active","archived"]);

        $response = $this->put(route('project.update', $project), [
            'name' => $name,
            'description' => $description,
            'avatar' => $avatar,
            'total_paid' => $total_paid,
            'total_requested' => $total_requested,
            'total_remaining' => $total_remaining,
            'min_donation_fee' => $min_donation_fee,
            'increment_by' => $increment_by,
            'bank_name' => $bank_name,
            'bank_branch' => $bank_branch,
            'bank_iban' => $bank_iban,
            'country' => $country,
            'city' => $city,
            'gov' => $gov,
            'lat' => $lat,
            'lng' => $lng,
            'status' => $status,
        ]);

        $project->refresh();

        $response->assertOk();

        $this->assertEquals($name, $project->name);
        $this->assertEquals($description, $project->description);
        $this->assertEquals($avatar, $project->avatar);
        $this->assertEquals($total_paid, $project->total_paid);
        $this->assertEquals($total_requested, $project->total_requested);
        $this->assertEquals($total_remaining, $project->total_remaining);
        $this->assertEquals($min_donation_fee, $project->min_donation_fee);
        $this->assertEquals($increment_by, $project->increment_by);
        $this->assertEquals($bank_name, $project->bank_name);
        $this->assertEquals($bank_branch, $project->bank_branch);
        $this->assertEquals($bank_iban, $project->bank_iban);
        $this->assertEquals($country, $project->country);
        $this->assertEquals($city, $project->city);
        $this->assertEquals($gov, $project->gov);
        $this->assertEquals($lat, $project->lat);
        $this->assertEquals($lng, $project->lng);
        $this->assertEquals($status, $project->status);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('project.destroy', $project));

        $response->assertNoContent();

        $this->assertModelMissing($project);
    }
}
