<aside class="w-full md:w-1/3 flex flex-col items-center px-3">

    <div class="w-full bg-white shadow flex flex-col my-4 p-6">
        <h3 class="text-xl font-semibold mb-3">
            All Categories
        </h3>
        @foreach ($categories as $category )
            <a href="{{route('by-category',$category)}}"
                class="hover:bg-blue-600 hover:text-white rounded py-2 px-4 mx-2
                {{ request('category')?->slug === $category->slug ? 'bg-blue-600 text-white' : ''}}"
            >
                {{$category->title}} ({{$category->total}})
            </a>
        @endforeach
    </div>

    <div class="w-full bg-white shadow flex flex-col my-4 p-6">
        <p class="text-xl font-semibold pb-5">
            {{ \App\Models\TextWidget::getTitle('about-us')}}
        </p>
        <p class="text-lg text-gray-600">
            {!! \App\Models\TextWidget::getContent('about-us') !!}
        </p>
        <a href="{{route('about-us')}}" class="w-full bg-blue-800 text-white font-bold text-sm uppercase rounded hover:bg-blue-700 flex items-center justify-center px-2 py-3 mt-4">
            Get know us
        </a>
    </div>

</aside>