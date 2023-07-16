<div class="">
    <livewire:comment-create :post="$post" />

    @foreach ($comments as $comment)
        <livewire:comment-item :comment="$comment" wire:key="comment-{{ $comment->id }}-{{ $comment->comments->count() }}" />

    @endforeach
    @if (count($comments) >= $commentsLoaded)
    <div class="text-center mt-4">
        <button wire:click="loadMoreComments" class="bg-blue-800 text-white font-bold text-sm uppercase rounded hover:bg-blue-700  items-center justify-center px-8 py-3 mt-4">
                Load more
            </button>
        </div>
    @endif




</div>
