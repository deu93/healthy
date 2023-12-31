<article class="bg-white flex w-full flex-col shadow my-4">
    <!-- Article Image -->
    <a class="{{ $width }} {{ $height }}" href="{{ route('view', $post) }}" class="hover:opacity-75">
        <img class="object-fill max-w-full h-full"  src="{{ $post->getThumbnail() }}" alt="{{ $post->title }}">
    </a>
    <div class="bg-white flex flex-col justify-start p-6">
        <div class="flex gap-4">

            @foreach ($post->categories as $category)

            <a href="{{ route('by-category', $category) }}" class="text-blue-700 text-sm font-bold uppercase pb-4">{{ $category->title }}</a>
            @endforeach
        </div>
        <a href="{{ route('view', $post) }}" class="text-3xl font-bold hover:text-gray-700 pb-4">{{ $post->title }}</a>

        @if ($showAuthor)

        <p href="#" class="text-sm pb-3">
            By <a href="#" class="font-semibold hover:text-gray-800">{{ $post->user->name }}</a>, Published on {{ $post->getFormatedDate() }} | {{ $post->human_read_time }}
        </p>
        @endif
        <p class="pb-6">{{ $post->shortBody() }}</p>
        <a href="{{ route('view', $post) }}" class="uppercase text-gray-900 hover:text-black">Continue Reading <i class="fas fa-arrow-right"></i></a>
    </div>
</article>
