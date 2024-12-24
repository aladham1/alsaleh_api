<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CommentController
 */
class CommentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected(): void
    {
        $comments = Comment::factory()->count(3)->create();

        $response = $this->get(route('comment.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CommentController::class,
            'store',
            \App\Http\Requests\CommentStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves(): void
    {
        $user = User::factory()->create();
        $content = $this->faker->paragraphs(3, true);

        $response = $this->post(route('comment.store'), [
            'user_id' => $user->id,
            'content' => $content,
        ]);

        $comments = Comment::query()
            ->where('user_id', $user->id)
            ->where('content', $content)
            ->get();
        $this->assertCount(1, $comments);
        $comment = $comments->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->get(route('comment.show', $comment));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CommentController::class,
            'update',
            \App\Http\Requests\CommentUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected(): void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();
        $content = $this->faker->paragraphs(3, true);
        $morphs = $this->faker->;
        $status = $this->faker->randomElement(/** enum_attributes **/);

        $response = $this->put(route('comment.update', $comment), [
            'user_id' => $user->id,
            'content' => $content,
            'morphs' => $morphs,
            'status' => $status,
        ]);

        $comment->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($content, $comment->content);
        $this->assertEquals($morphs, $comment->morphs);
        $this->assertEquals($status, $comment->status);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->delete(route('comment.destroy', $comment));

        $response->assertNoContent();

        $this->assertModelMissing($comment);
    }
}
