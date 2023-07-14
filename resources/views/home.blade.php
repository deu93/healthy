<x-app-layout meta-description="The Deu's personal blog about healthy life">
    <div class="container max-w-7xl mx-auto py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            {{-- Latest Post --}}
            @if($latestPost)
                <div class="col-span-3">
                    <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1 border-b-2 border-blue-500 mb-3">
                        Latest Post
                    </h2>
                    @php
                        $height = 'h-3/5';
                    @endphp
                    <x-post-item :post="$latestPost" :height="$height"/>
                </div>
            @endif

            {{-- Popular 3 posts --}}
                @if($popularPosts)
                <div class="">
                    <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1 border-b-2 border-blue-500 mb-3">
                        Polular Posts
                    </h2>

                    @foreach ($popularPosts as $post)
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            <div class="pt-1">
                                <a href="{{ route('view', $post) }}">
                                    <img src="{{ $post->getThumbnail() }}" alt="{{ $post->title }}"/>
                                </a>
                            </div>
                            <div class="col-span-3">
                                <a href="{{ route('view', $post) }}"><h3 class="font-semibold truncate">{{ $post->title }}</h3></a>
                                <div class="flex gap-2">
                                    @if ($post->categories)
                                        @foreach ($post->categories->take(3) as $category)
                                            <a href="{{ route('by-category', $category) }}" class="bg-blue-500 text-white p-1 rounded text-xs font-semibold uppercase">{{ $category->title }}</a>
                                        @endforeach
                                    @endif

                                </div>

                                <div class="text-xs">
                                    {{ $post->shortBody(10) }}
                                </div>
                                <a href="{{ route('view', $post) }}" class="uppercase text-gray-700 text-xs hover:text-black">Continue Reading <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
        </div>



        {{-- Recomended posts --}}
        @if($recommendedPosts)
        <div class="mb-8">
            <h2 class="text-xl font-bold sm:text-xl font-bold text-blue-500 uppercase pb-1 border-b-2 border-blue-500 mb-3">
                Recomended Posts
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach ($recommendedPosts as $post)
                @php
                    $width='w-full';
                @endphp
                    <x-post-item :post="$post" :show-author="false" :width="$width"/>
                @endforeach

            </div>
        </div>
        @endif


        {{-- Latest categories --}}
        @if ($categories)


        @foreach ($categories as $category)
        <div class="mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-blue-500 uppercase pb-1 border-b-2 border-blue-500 mb-3">
                Post for Category "{{ $category->title }}" <a href="{{ route('by-category', $category) }}">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </h2>


                <div class="mb-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                        @foreach ($category->posts as $post)
                                @php
                                    $width='w-full';
                                @endphp
                                <x-post-item :post="$post" :show-author="false" :width="$width" />

                        @endforeach
                    </div>
                </div>
        </div>
            @endforeach
        @endif




    </div>



</x-app-layout>
