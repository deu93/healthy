<x-app-layout meta-title="About Healthy blog" meta-description="About us page">
    <!-- Post Section -->
    <section class="w-full flex flex-col items-center px-3">

        <article class="flex w-full flex-col shadow my-4">
            <!-- Article Image -->
            <a href="#" class="hover:opacity-75">

                <img src="/storage/{{$widget->image}}" class="max-w-7xl">
            </a>
          <div class="bg-white flex flex-col justify-start p-6">

            <h1  class="text-3xl font-bold hover:text-gray-700 pb-4">{{ $widget->title }}</h1>

                <div class="">
                    {!! $widget->content !!}
                </div>
            </div>
        </article>


    </section>





</x-app-layout>
