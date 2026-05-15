<div>
    <!-- TopNavBar (Hidden on Mobile, Visible on Web) -->
    <nav class="hidden md:flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 backdrop-blur-md bg-[#f4fbf7]/95 border-b-4 border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
        <div class="flex items-center gap-8">
            <a class="text-2xl font-black tracking-tighter text-[#012d1d] font-headline uppercase" href="/">Dapute</a>
            <div class="flex gap-6">
                @foreach($links as $link)
                    <a class="font-headline font-black tracking-tight uppercase text-[#012d1d]/70 hover:text-[#f4fbf7] hover:bg-[#012d1d] transition-all duration-200 px-3 py-1 border-[3px] border-transparent hover:border-[#012d1d] hover:-translate-y-0.5 hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" href="{{ $link['url'] }}">
                        {{ $link['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-4 text-[#012d1d]">
            <button class="hover:text-[#f4fbf7] hover:bg-[#012d1d] border-[3px] border-transparent hover:border-[#012d1d] transition-all duration-200 p-2 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none cursor-pointer">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>
            <a href="/profile" class="text-[#012d1d] hover:text-[#f4fbf7] hover:bg-[#012d1d] border-[3px] border-transparent hover:border-[#012d1d] transition-all duration-200 p-2 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none cursor-pointer">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">account_circle</span>
            </a>
        </div>
    </nav>

    <!-- BottomNavBar (Visible on Mobile, Hidden on Web) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex h-16 bg-[#f4fbf7] border-t-4 border-[#012d1d]">
        @foreach($links as $link)
            <a class="flex-1 flex flex-col items-center justify-center text-[#012d1d]/70 py-2 h-full hover:bg-[#012d1d] hover:text-[#f4fbf7] active:scale-95 transition-all duration-200" href="{{ $link['url'] }}">
                <span class="material-symbols-outlined mb-1 text-xl">{{ $link['icon'] }}</span>
                <span class="font-label font-bold text-[10px] uppercase">{{ $link['name'] }}</span>
            </a>
        @endforeach
        <!-- Static Profile Link for Mobile -->
        <a class="flex-1 flex flex-col items-center justify-center text-[#012d1d]/70 py-2 h-full hover:bg-[#012d1d] hover:text-[#f4fbf7] active:scale-95 transition-all duration-200" href="/profile">
            <span class="material-symbols-outlined mb-1 text-xl" style="font-variation-settings: 'FILL' 1;">person</span>
            <span class="font-label font-bold text-[10px] uppercase">Profile</span>
        </a>
    </nav>
</div>
