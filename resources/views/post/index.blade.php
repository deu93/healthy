<?php
    // @var $posts \Illuminate\Pagination\LenghtAwarePaginator
?>

<x-app-layout :meta-title="'Healthy blog - posts by category ' . $category->title" :meta-description="'Sorted by category - ' . strToLower($category->title)">
    <!-- Posts Section -->
<section class="w-full md:w-2/3 flex flex-col items-center px-3">

    @foreach ($posts as $post)
        <x-post-item :post="$post">

        </x-post-item>
    @endforeach


        {{ $posts->onEachSide(1)->links() }}


        <!-- Sidebar Section -->

</section>

<x-sidebar>

</x-sidebar>
</x-app-layout>