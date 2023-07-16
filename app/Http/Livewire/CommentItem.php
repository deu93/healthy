<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;

class CommentItem extends Component
{
    public Comment $comment;

    protected $listeners = [
        'cancelEditing' => 'cancelEditing',
        'commentUpdated' => 'commentUpdated',
        'commentCreated' => 'commentCreated'
    ];



    public bool $reply = false;
    public bool $editing = false;

    public function mount(Comment $comment)
    {
        $this->comment = $comment;
    }


    public function render()
    {
        return view('livewire.comment-item');
    }

    public function deleteComment()
    {
        $user = auth()->user();
        if(!$user) {
            return redirect('/login');
        }

        if($this->comment->user_id != $user->id){
            return response('You are not prepeared!', 403);
        }

        $id = $this->comment->id;
        $this->comment->delete();
        $this->emitUp('commentDeleted', $id);
    }

    public function startCommentEdit()
    {
        $this->editing = true;
    }

    public function cancelEditing()
    {
        $this->editing = false;
        $this->reply = false;
    }

    public function commentUpdated ()
    {
        $this->editing = false;
    }

    public function startReply()
    {
        $this->reply = true;
    }

    public function commentCreated() {
        $this->reply = false;
    }

}

