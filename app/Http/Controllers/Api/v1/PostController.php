<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\IndexPostRequest;
use App\Http\Requests\Api\v1\StorePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct()
    {
        return $this->authorizeResource(Post::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexPostRequest $request)
    {
        $data = $request->validated();

        return response()->json([
            'page' => $data['page'] ?? 0,
            'size' => $data['size'] ?? 10,
            'posts' => Post::query()
                ->with([
                    'user',
                    'post_attachments',
                ])
                ->offset($data['page'] ?? 0)
                ->limit($data['size'] ?? 10)
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $post = Post::create([
                'user_id' => $request->user()->id,
                'caption' => $data['caption'],
            ]);

            /** @var \Illuminate\Http\UploadedFile $attachment */
            foreach ($data['attachments'] as $attachment) {
                $post->post_attachments()->create([
                    'storage_path' => $attachment->store(date('Y-m-d') . '/uploads'),
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Post failed to be created: ' . $th->getMessage(),
            ], 500);
        }

        DB::commit();

        return response()->json([
            'message' => 'Post created successfully',
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            DB::beginTransaction();

            $post->post_attachments()->delete();

            $post->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Post failed to be deleted: ' . $th->getMessage(),
            ], 500);
        }

        DB::commit();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
