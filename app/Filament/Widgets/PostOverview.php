<?php

namespace App\Filament\Widgets;

use App\Models\PostView;
use App\Models\UpvoteDownvote;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;


class PostOverview extends Widget
{
    protected array|string|int $columnSpan = 3;
    public ?Model $record = null;
    protected function getViewData() :array
    {
        return[
            'view_count' => PostView::where('post_id','=',$this->record->id)->count(),
            'upvotes' => UpvoteDownvote::where('post_id', '=', $this->record->id)->where('is_upvote', '=', 1)->count(),
            'downvotes' => UpvoteDownvote::where('post_id', '=', $this->record->id)->where('is_upvote', '=', 0)->count(),
        ];
    }
    protected static string $view = 'filament.widgets.post-overview';
}
