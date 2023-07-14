<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Post;
use App\Models\Category;
use App\Models\PostView;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UpvoteDownvote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function home():  View
    {
            // Latest post
        $latestPost = Post::where('active', 1)
            ->where('published_at', '<', Carbon::now()->setTimezone('Europe/Kyiv'))
            ->orderBy('published_at', 'desc')
            ->first();


        $parameters = [
            'active' => 1,
            'publishedAt' => Carbon::now()
        ];

            // Show the most popular 3 posts based on upvotes
        $popularPosts = Post::query()
            ->leftJoin('upvote_downvotes', 'posts.id', '=', 'upvote_downvotes.post_id')
            ->select('posts.*',
                DB::raw('COUNT(CASE WHEN upvote_downvotes.post_id = posts.id AND upvote_downvotes.is_upvote = 1 THEN 1 END) as upvote_count'),
                DB::raw('COUNT(CASE WHEN upvote_downvotes.post_id = posts.id AND upvote_downvotes.is_upvote = 0 THEN 1 END) as downvote_count')
            )
            ->where('posts.active', '=', $parameters['active'])
            ->where('posts.published_at', '<', $parameters['publishedAt'])
            ->havingRaw('upvote_count > downvote_count')
            ->orderByDesc('upvote_count')
            ->groupBy('posts.id')
            ->limit(5)
            ->get();




        $user = auth()->user();

        if($user){
            $likes = UpvoteDownvote::query()
                ->where('user_id', $user->id)
                ->where('is_upvote', '=', 1)
                ->count();
            if($likes) {
                // If authorized - Show recommended posts based on user upvotes

                //If upvotes exists
                $leftJoin = "(SELECT cp.category_id, cp.post_id FROM upvote_downvotes JOIN category_post cp ON upvote_downvotes.post_id = cp.post_id WHERE upvote_downvotes.is_upvote = 1 AND upvote_downvotes.user_id = :user_id) as t";

                $recommendedPosts = Post::query()
                    ->leftJoin('category_post as cp', 'posts.id', '=', 'cp.post_id')
                    ->select('posts.*')
                    ->distinct()
                    ->whereNotExists(function ($query) use ($leftJoin) {
                        $query->select(DB::raw(1))
                            ->from(DB::raw($leftJoin))
                            ->whereRaw('posts.id = t.post_id');
                    })
                    ->setBindings(['user_id' => $user->id])
                    ->limit(3)
                    ->get();
                }else{
                    // Popular posts based on views
                    $recommendedPosts = Post::query()
                    ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
                    ->select('posts.*',  DB::raw('COUNT(post_views.id) as view_count'))
                    ->where('posts.active', '=', 1)
                    ->where('posts.published_at', '<', Carbon::now())
                    ->orderByDesc('view_count')
                    ->groupBy('posts.id')
                    ->limit(3)
                    ->get();
                }




        } else {
            // Not authorized - Popular posts based on views
            $recommendedPosts = Post::query()
                ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
                ->select('posts.*',  DB::raw('COUNT(post_views.id) as view_count'))
                ->where('posts.active', '=', 1)
                ->where('posts.published_at', '<', Carbon::now())
                ->orderByDesc('view_count')
                ->groupBy('posts.id')
                ->limit(3)
                ->get();
        }

        // Show recent categories whith their latest posts

        $categories = Category::query()
            ->whereHas('posts', function($query){
                $query->where('active','=', 1)
                ->where('published_at', '<', Carbon::now());
            })
            ->select('categories.*')
            ->selectRaw('MAX(posts.published_at) as max_date')
            ->leftJoin('category_post', 'categories.id', '=', 'category_post.category_id')
            ->leftJoin('posts', 'posts.id', '=', 'category_post.post_id')
            ->where('posts.published_at', '<', Carbon::now())
            ->where('posts.active', '=', 1)
            ->groupBy('categories.id')
            ->orderByDesc('max_date')
            ->limit(6)
            ->get();

        foreach ($categories as $category) {
            $category->load(['posts' => function ($query) {
                $query->where('published_at', '<', Carbon::now())
                    ->where('active', '=', 1)
                    ->orderByDesc('published_at')
                    ->take(3);
            }]);
        }




        return view('home', compact('latestPost', 'popularPosts', 'recommendedPosts', 'categories'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, Request $request)
    {

        if(!$post->active || $post->published_at > Carbon::now())
        {
            throw new NotFoundHttpException();
        }

        $next = Post::query()
        ->where('published_at', '<=', Carbon::now())
        ->where('published_at', '<', $post->published_at)
        ->orderBy('published_at', 'desc')
        ->limit(1)
        ->first();
        $prev = Post::query()
        ->where('published_at', '<=', Carbon::now())
        ->where('published_at', '>', $post->published_at)
        ->orderBy('published_at', 'asc')
        ->limit(1)
        ->first();

        $user = $request->user();

            $cookie_name = (Str::replace('.','',($request->ip())).'-'. $post->id);


            if(Cookie::get($cookie_name) == ''){
                $cookie = cookie($cookie_name, '1', 60);
                PostView::create([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'post_id' => $post->id,
                'user_id' => $user?->id
                ]);
                return response()->view('post.view', compact('post','prev','next'))->withCookie($cookie);
            }else{
                return view('post.view', compact('post','prev','next'));
            }





    }

   public function byCategory(Category $category)
   {
        $posts = Post::query()
        ->join('category_post', 'posts.id', '=', 'category_post.post_id')
        ->where('category_post.category_id', '=', $category->id)
        ->where('active', '=', true)
        ->where('published_at', '<=', Carbon::now())
        ->orderBy('published_at', 'desc')
        ->paginate(10);

        return view('post.index', compact('posts', 'category'));
   }
}
