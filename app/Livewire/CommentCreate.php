<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CommentCreate extends Component
{
    public Post $post;
    public string $comment = '';
    public ?Comment $commentModel = null;
    public ?Comment $parentComment = null;

    public function mount(Post $post, $commentModel = null,$parentComment=null)
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
        if (!$user) {
            return $this->redirect('/login');
        }

        if ($this->commentModel) {
            if($this->commentModel->user_id != $user->id){
                return response('You are not allowed to perform this action',403);
            }

            $this->commentModel->comment = $this->comment;
            $this->commentModel->save();
            $this->comment = '';
            $this->dispatch('commentUpdated');
        } else {
            $comment = Comment::create([
                'comment' => $this->comment,
                'post_id' => $this->post->id,
                'user_id' => $user->id,
                'parent_id'=>$this->parentComment?->id
            ]);
            $id = $comment->id;
            $this->dispatch('commentCreated', $id);

            $this->comment = '';
        }
    }
}
