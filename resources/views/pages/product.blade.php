@extends('master')
@section('main')
    <section>
        <div class="flex justify-start">
            <h1 class="font-bold uppercase leading-tight ml-5 my-auto text-red-700">cafe anonim</h1>
            <p class="ml-auto bg-red-950 text-white text-center uppercase font-bold text-lg py-1 pr-3 pl-5 rounded-bl-xl">Meja 
                <span>
                    @if (isset($tableNo))
                        {{$tableNo}}
                    @endif
                </span>
            </p>
        </div>
        <div class="mt-10 pl-5">
            @foreach ($product as $item)
                <div class="mb-8">
                    <p class="text-sm font-bold mb-4 uppercase text-red-900">{{$item->title}}</p>
                    <div id="scroll-container" class="overflow-x-auto whitespace-nowrap scroll-hidden cursor-grab">
                        @foreach ($item->product as $product)
                            <div class="inline-block mr-2 h-fit align-top mb-auto ml-1 my-1 rounded max-w-40 truncate">
                                <img class="h-28 mb-2" src="{{asset('storage/'. $product->path)}}" alt="{{$product->id}}">
                                @if($product->m_status_tabs_id == 3)
                                    <p class="rounded-lg bg-red-50 border border-red-300 mb-1 text-[9px] font-semibold w-fit text-red-700 px-1.5 py-1">{{ $product->status->title }}</p>
                                @endif
                                <p class="font-semibold text-sm leading-tight">{{ $product->name }}</p>
                                <span class="font-normal text-[11px] truncate max-w-40">{{$product->desc}}</span>
                                <p class="text-sm font-bold mt-1">Rp. {{ $product->price }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <script>
        const slider = document.getElementById('scroll-container');
        let isDown = false;
        let startX;
        let scrollLeft;
    
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('cursor-grabbing');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
    
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('cursor-grabbing');
        });
    
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('cursor-grabbing');
        });
    
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5; // Speed
            slider.scrollLeft = scrollLeft - walk;
        });
    </script>
@endsection