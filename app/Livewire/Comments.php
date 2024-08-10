<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class Comments extends Component
{
    public Post $post;

    protected $listeners = [
        'commentCreated' => '$refresh',
        'commentDeleted' => '$refresh',
    ];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->comments = $this->selectComments();
    }

    public function render()
    {
        $comments = $this->selectComments();
        // dd($comments[1]->comments->count());
        return view('livewire.comments',compact('comments'));
    }

    public function selectComments(){
        return Comment::where('post_id', $this->post->id)
                        ->with(['post','user','comments'])
                        ->whereNull('parent_id')
                        ->orderByDesc('created_at')
                        ->get();
    }
}
