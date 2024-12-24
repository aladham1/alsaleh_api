<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BlogController
 */
class BlogControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $blogs = Blog::factory()->count(3)->create();

        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BlogController::class,
            'store',
            \App\Http\Requests\BlogStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves(): void
    {
        $content = $this->faker->paragraphs(3, true);

        $response = $this->post(route('blog.store'), [
            'content' => $content,
        ]);

        $blogs = Blog::query()
            ->where('content', $content)
            ->get();
        $this->assertCount(1, $blogs);
        $blog = $blogs->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $blog = Blog::factory()->create();

        $response = $this->get(route('blog.show', $blog));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BlogController::class,
            'update',
            \App\Http\Requests\BlogUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $blog = Blog::factory()->create();
        $content = $this->faker->paragraphs(3, true);

        $response = $this->put(route('blog.update', $blog), [
            'content' => $content,
        ]);

        $blog->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($content, $blog->content);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $blog = Blog::factory()->create();

        $response = $this->delete(route('blog.destroy', $blog));

        $response->assertNoContent();

        $this->assertModelMissing($blog);
    }
}
