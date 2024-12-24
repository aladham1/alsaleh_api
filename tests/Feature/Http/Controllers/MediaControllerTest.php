<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Medium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\MediaController
 */
class MediaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $media = Media::factory()->count(3)->create();

        $response = $this->get(route('medium.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MediaController::class,
            'store',
            \App\Http\Requests\MediaStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves(): void
    {
        $morphs = $this->faker->;
        $type = $this->faker->randomElement(/** enum_attributes **/);

        $response = $this->post(route('medium.store'), [
            'morphs' => $morphs,
            'type' => $type,
        ]);

        $media = Medium::query()
            ->where('morphs', $morphs)
            ->where('type', $type)
            ->get();
        $this->assertCount(1, $media);
        $medium = $media->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $medium = Media::factory()->create();

        $response = $this->get(route('medium.show', $medium));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MediaController::class,
            'update',
            \App\Http\Requests\MediaUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $medium = Media::factory()->create();
        $morphs = $this->faker->;
        $type = $this->faker->randomElement(/** enum_attributes **/);

        $response = $this->put(route('medium.update', $medium), [
            'morphs' => $morphs,
            'type' => $type,
        ]);

        $medium->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($morphs, $medium->morphs);
        $this->assertEquals($type, $medium->type);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $medium = Media::factory()->create();
        $medium = Medium::factory()->create();

        $response = $this->delete(route('medium.destroy', $medium));

        $response->assertNoContent();

        $this->assertModelMissing($medium);
    }
}
