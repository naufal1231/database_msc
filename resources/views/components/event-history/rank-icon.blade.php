<div class="flex flex-col items-center">
    @if($rank == 1)
        <div class="relative">
            <div class="w-14 h-14 rank-1 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-lg font-black text-white drop-shadow-md">
                    {{ $rank }}
                </span>
            </div>
            <div class="text-xs font-bold text-yellow-900 mt-2 tracking-wide">JUARA 1</div>
        </div>
    @elseif($rank == 2)
        <div class="relative">
            <div class="w-12 h-12 rank-2 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-base font-black text-white drop-shadow-md">
                    {{ $rank }}
                </span>
            </div>
            <div class="text-xs font-bold text-gray-800 mt-2 tracking-wide">JUARA 2</div>
        </div>
    @elseif($rank == 3)
        <div class="relative">
            <div class="w-10 h-10 rank-3 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-900" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-sm font-black text-white drop-shadow-md">
                    {{ $rank }}
                </span>
            </div>
            <div class="text-xs font-bold text-orange-800 mt-2 tracking-wide">JUARA 3</div>
        </div>
    @else
        <div class="flex flex-col items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                <span class="text-lg font-black text-white drop-shadow-md">
                    {{ $rank }}
                </span>
            </div>
            <div class="text-xs font-semibold text-blue-700 mt-1">PERINGKAT</div>
        </div>
    @endif
</div>