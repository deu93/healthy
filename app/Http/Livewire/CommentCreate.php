<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;


class CommentCreate extends Component
{
    public string $comment = '';
    public Post $post;

    public ?Comment $commentModel = null;
    public ?Comment $parentComment = null;



    public function mount(Post $post, $commentModel = null, $parentComment = null)
    {
        $this->post = $post;
        $this->commentModel = $commentModel;
        $this->comment = $commentModel ? $commentModel->comment : '';

        $this->parentComment = $parentComment;
    }


    public function render()
    {
        return view('livewire.comment-create');
    }

    public function createComment()
    {
        $user = auth()->user();
        if(!$user) {
            return redirect('/login');
        }
        if($this->commentModel){
            if($this->commentModel->user_id != $user->id){
                return response('You are not prepeared!', 403);
            }
            $this->validate([
                'comment' => ['required', 'string', 'not_regex:/http(s)?:\/\/\S+/i'],
            ], [
                'comment.not_regex' => 'No links please!',
            ]);
            $this->commentModel->comment = $this->comment;
            $this->commentModel->save();

            $this->comment = '';
            $this->emitUP('commentUpdated');


        } else{


        $this->validate([
            'comment' => ['required', 'string', 'not_regex:/http(s)?:\/\/\S+/i'],
        ], [
            'comment.not_regex' => 'No links please!',
        ]);


        $comment = Comment::create([
            'comment' => $this->comment,
            'post_id' => $this->post->id,
            'user_id' => $user->id,
            'parent_id' => $this->parentComment?->id
        ]);
        $this->emitUp('commentCreated', $comment->id);
        $this->comment = '';
        }

    }
}
