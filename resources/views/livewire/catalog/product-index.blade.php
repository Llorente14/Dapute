<div x-data="{
        showAddProduct: false,
        imagePreview: null,
        isActive: true,
        handleImage(e) {
            const file = e.target.files[0];
            if (!file) return;
            const r = new FileReader();
            r.onload = ev => { this.imagePreview = ev.target.result; };
            r.readAsDataURL(file);
        }
    }"
    x-init="$watch('showAddProduct', val => document.body.style.overflow = val ? 'hidden' : '')">

    <div class="p-6 md:p-12 lg:p-16 flex flex-col gap-12 animate-fade-up">
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b-4 border-[#012d1d] pb-8 opacity-0 animate-fade-up [animation-delay:100ms]">
        <div class="flex flex-col gap-2 max-w-2xl">
            <h2 class="font-headline text-5xl md:text-6xl text-[#012d1d] font-black tracking-tight leading-none">Product Inventory</h2>
            <p class="font-body text-lg text-[#414844] max-w-xl">Manage your bakery's active cookie selection, adjust pricing, and control availability across all Dapute storefronts.</p>
        </div>
        <button @click="showAddProduct = true" class="bg-[#012d1d] text-[#ffffff] border-[3px] border-[#012d1d] font-label uppercase text-sm px-6 py-3 tracking-wider hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)] transition-all duration-300 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] active:translate-y-0 active:translate-x-0 active:shadow-none flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">add</span>
            Add New Product
        </button>
    </header>

    <!-- Product Table Grid (Bento/Brutalist) -->
    <section class="flex flex-col gap-8 md:gap-0 md:grid md:grid-cols-1 md:border-t-[3px] md:border-l-[3px] border-[#012d1d] bg-transparent md:bg-[#012d1d] opacity-0 animate-fade-up [animation-delay:200ms] shadow-none md:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)]">
        <!-- Table Header (Desktop) -->
        <div class="hidden md:grid grid-cols-12 gap-0 border-b-[3px] border-r-[3px] border-[#012d1d] bg-[#dde4e0]">
            <div class="col-span-1 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Img</span>
            </div>
            <div class="col-span-4 p-4 border-r-[3px] border-[#012d1d] flex items-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Product Name &amp; Desc</span>
            </div>
            <div class="col-span-3 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-end">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Base Price</span>
            </div>
            <div class="col-span-2 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Status</span>
            </div>
            <div class="col-span-2 p-4 flex items-center justify-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Actions</span>
            </div>
        </div>

        <!-- Product Row 1 -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-0 border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d] bg-[#f4fbf7] hover:bg-[#d8e2dc] transition-colors duration-300 group shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] md:shadow-none">
            <div class="md:col-span-1 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-center bg-[#eef5f1] group-hover:bg-transparent transition-colors">
                <img alt="Sea Salt Chocolate Chunk" class="w-16 h-16 object-cover border-[3px] border-[#012d1d] aspect-square shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] group-hover:scale-110 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCkntqyD0bjIY7vdIX5L7eCcyxXX0mBh9k3_-a6Iqwl5tEDv3yqI6Vx3RMcwEGJPZvCXORHt6NgdIcQOPxFdLKPkrxWcDgJ_7LsPVA-cP4nz-wB8Kvq90M5F2BqTwfE0ZPeLA39TThhxaqtBznp9Yj0athFZEn6jRCc1HuwGLEIALwYoImNVxaCLB9_vV5pQjCFeLQT8T5uNd3Wx1l3IGRdeaj8Eo4d13H7RqP-a3GTG8FlWsLkoIfpY4tArCwT1w1mGD8heCK6VdAW"/>
            </div>
            <div class="md:col-span-4 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex flex-col justify-center gap-1 group-hover:pl-6 transition-all duration-300">
                <h3 class="font-headline font-black text-xl text-[#012d1d] leading-tight">Sea Salt Chocolate Chunk</h3>
                <p class="font-body text-sm text-[#414844] line-clamp-2">Our signature dark chocolate chunk cookie finished with flaky Maldon sea salt.</p>
            </div>
            <div class="md:col-span-3 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center md:justify-end">
                <div class="flex items-center w-full md:w-auto relative border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:border-[#012d1d] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                    <span class="absolute left-3 text-[#414844] font-label text-sm font-bold">Rp</span>
                    <input class="w-full md:w-32 bg-transparent border-0 font-label text-right pl-10 pr-3 py-2 text-[#414844] focus:text-[#012d1d] focus:font-bold focus:outline-none transition-all duration-300" type="text" value="85.000"/>
                </div>
            </div>
            <div class="md:col-span-2 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-between md:justify-center">
                <span class="md:hidden font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Status</span>
                <label class="relative inline-flex items-center cursor-default hover:scale-105 transition-transform">
                    <div class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] bg-[#d3ee6f] shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] pointer-events-none">
                        <span class="absolute top-[2px] w-5 h-5 border-[3px] border-[#012d1d] bg-[#012d1d] left-[calc(100%-1.375rem)]"></span>
                    </div>
                    <span class="ml-3 text-sm font-label font-bold text-[#012d1d] uppercase tracking-wider hidden lg:block">Active</span>
                </label>
            </div>
            <div class="md:col-span-2 p-4 flex items-center justify-end md:justify-center gap-2">
                <a href="#" aria-label="Edit" class="bg-[#ffffff] border-[3px] border-[#012d1d] p-2 text-[#012d1d] hover:bg-[#012d1d] hover:text-[#ffffff] transition-all duration-300 flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:-translate-y-0.5 active:translate-y-0 active:translate-x-0 active:shadow-none">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </a>
                <button aria-label="Delete" class="bg-[#ffffff] border-[3px] border-[#ba1a1a] p-2 text-[#ba1a1a] hover:bg-[#ba1a1a] hover:text-[#ffffff] transition-all duration-300 flex items-center justify-center shadow-[2px_2px_0px_0px_#ba1a1a] hover:shadow-[4px_4px_0px_0px_#ba1a1a] hover:-translate-y-0.5 active:translate-y-0 active:translate-x-0 active:shadow-none">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </div>
        </div>

        <!-- Product Row 2 -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-0 border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d] bg-[#f4fbf7] hover:bg-[#d8e2dc] transition-colors duration-300 group shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] md:shadow-none">
            <div class="md:col-span-1 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-center bg-[#eef5f1] group-hover:bg-transparent transition-colors">
                <img alt="Brown Butter Oatmeal" class="w-16 h-16 object-cover border-[3px] border-[#012d1d] aspect-square shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] group-hover:scale-110 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC1jWxVYEUM6w6UtP6g0QCj13uMJ8rdR6K8CIqPohkex1iEmNwkSoc5QE9eY-LQfV8dcngILrOVmv_Btyl5YTaTUc2VDlGV9trRUazEaS3axPjSzZ7_ZQftWHfUmBNbivyOIV8n6fcndxNwDxmw1ftaBAdG23F9I6rlmmCMnlKv1pRi_oTvlspOGMFaog60BQ422g1AC9WbmRrafBxZ8fI13mu-HIdUSdmCPsALq6nYbCrGPapvulUX0KkW8rqeQdspmBmUBCRpWqT3"/>
            </div>
            <div class="md:col-span-4 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex flex-col justify-center gap-1 group-hover:pl-6 transition-all duration-300">
                <h3 class="font-headline font-black text-xl text-[#012d1d] leading-tight">Brown Butter Oatmeal</h3>
                <p class="font-body text-sm text-[#414844] line-clamp-2">Chewy oats mixed with golden raisins and nut-brown butter.</p>
            </div>
            <div class="md:col-span-3 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center md:justify-end">
                <div class="flex items-center w-full md:w-auto relative border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:border-[#012d1d] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                    <span class="absolute left-3 text-[#414844] font-label text-sm font-bold">Rp</span>
                    <input class="w-full md:w-32 bg-transparent border-0 font-label text-right pl-10 pr-3 py-2 text-[#414844] focus:text-[#012d1d] focus:font-bold focus:outline-none transition-all duration-300" type="text" value="75.000"/>
                </div>
            </div>
            <div class="md:col-span-2 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-between md:justify-center">
                <span class="md:hidden font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Status</span>
                <label class="relative inline-flex items-center cursor-default hover:scale-105 transition-transform">
                    <div class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] bg-[#d3ee6f] shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] pointer-events-none">
                        <span class="absolute top-[2px] w-5 h-5 border-[3px] border-[#012d1d] bg-[#012d1d] left-[calc(100%-1.375rem)]"></span>
                    </div>
                    <span class="ml-3 text-sm font-label font-bold text-[#012d1d] uppercase tracking-wider hidden lg:block">Active</span>
                </label>
            </div>
            <div class="md:col-span-2 p-4 flex items-center justify-end md:justify-center gap-2">
                <a href="#" aria-label="Edit" class="bg-[#ffffff] border-[3px] border-[#012d1d] p-2 text-[#012d1d] hover:bg-[#012d1d] hover:text-[#ffffff] transition-all duration-300 flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:-translate-y-0.5 active:translate-y-0 active:translate-x-0 active:shadow-none">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </a>
                <button aria-label="Delete" class="bg-[#ffffff] border-[3px] border-[#ba1a1a] p-2 text-[#ba1a1a] hover:bg-[#ba1a1a] hover:text-[#ffffff] transition-all duration-300 flex items-center justify-center shadow-[2px_2px_0px_0px_#ba1a1a] hover:shadow-[4px_4px_0px_0px_#ba1a1a] hover:-translate-y-0.5 active:translate-y-0 active:translate-x-0 active:shadow-none">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </div>
        </div>

        <!-- Product Row 3 -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-0 border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d] bg-[#dde4e0] opacity-75 hover:opacity-100 transition-colors duration-300 group shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] md:shadow-none">
            <div class="md:col-span-1 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-center bg-[#d5dcd8] group-hover:bg-transparent transition-colors">
                <div class="w-16 h-16 bg-[#f4fbf7] border-[3px] border-[#012d1d] flex items-center justify-center aspect-square shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-300">
                    <span class="material-symbols-outlined text-[#717973]">image</span>
                </div>
            </div>
            <div class="md:col-span-4 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex flex-col justify-center gap-1 group-hover:pl-6 transition-all duration-300">
                <h3 class="font-headline font-black text-xl text-[#012d1d] leading-tight line-through decoration-2 decoration-[#717973] group-hover:line-through">Matcha Macadamia</h3>
                <p class="font-body text-sm text-[#414844] line-clamp-2">Seasonal specialty. Earthy matcha infused dough with roasted macadamia nuts.</p>
            </div>
            <div class="md:col-span-3 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center md:justify-end">
                <div class="flex items-center w-full md:w-auto relative border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:border-[#012d1d] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                    <span class="absolute left-3 text-[#414844] font-label text-sm font-bold">Rp</span>
                    <input class="w-full md:w-32 bg-transparent border-0 font-label text-right pl-10 pr-3 py-2 text-[#414844] focus:text-[#012d1d] focus:font-bold focus:outline-none transition-all duration-300 cursor-not-allowed" type="text" value="95.000" disabled/>
                </div>
            </div>
            <div class="md:col-span-2 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-between md:justify-center">
                <span class="md:hidden font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Status</span>
                <label class="relative inline-flex items-center cursor-not-allowed hover:scale-105 transition-transform">
                    <div class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] bg-[#dde4e0] shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] pointer-events-none">
                        <span class="absolute top-[2px] w-5 h-5 border-[3px] border-[#012d1d] bg-[#717973] left-[1px]"></span>
                    </div>
                    <span class="ml-3 text-sm font-label font-bold text-[#717973] uppercase tracking-wider hidden lg:block">Draft</span>
                </label>
            </div>
            <div class="md:col-span-2 p-4 flex items-center justify-end md:justify-center gap-2">
                <a href="#" aria-label="Edit" class="bg-[#ffffff] border-[3px] border-[#012d1d] p-2 text-[#012d1d] hover:bg-[#012d1d] hover:text-[#ffffff] transition-all duration-300 flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:-translate-y-0.5 active:translate-y-0 active:translate-x-0 active:shadow-none">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </a>
                <button aria-label="Delete" class="bg-[#ffffff] border-[3px] border-[#ba1a1a] p-2 text-[#ba1a1a] hover:bg-[#ba1a1a] hover:text-[#ffffff] transition-all duration-300 flex items-center justify-center shadow-[2px_2px_0px_0px_#ba1a1a] hover:shadow-[4px_4px_0px_0px_#ba1a1a] hover:-translate-y-0.5 active:translate-y-0 active:translate-x-0 active:shadow-none">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div class="flex items-center justify-between p-4 border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d] bg-[#d8e2dc] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] md:shadow-none">
            <button aria-label="Previous Page" class="w-10 h-10 flex items-center justify-center border-[3px] border-[#012d1d] bg-[#ffffff] text-[#012d1d] hover:bg-[#012d1d] hover:text-[#ffffff] transition-colors shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] active:translate-y-0.5 active:translate-x-0.5 active:shadow-none">
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </button>
            <p class="font-label text-xs text-[#5b6560] uppercase tracking-widest text-center font-bold">Showing 3 of 12 Products</p>
            <button aria-label="Next Page" class="w-10 h-10 flex items-center justify-center border-[3px] border-[#012d1d] bg-[#ffffff] text-[#012d1d] hover:bg-[#012d1d] hover:text-[#ffffff] transition-colors shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] active:translate-y-0.5 active:translate-x-0.5 active:shadow-none">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </button>
        </div>
    </section>
    </div>

    <!-- ADD PRODUCT POPUP MODAL -->
    <div x-show="showAddProduct" x-cloak
         class="fixed inset-y-0 right-0 z-[100] flex items-center justify-center p-4 sm:p-6 transition-all duration-300 backdrop-blur-3xl bg-[#f4fbf7]/95"
         :class="sidebarOpen ? 'md:left-64 left-0' : 'md:left-20 left-0'"
         x-transition:enter="ease-out duration-100"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-75"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="showAddProduct = false">

        <!-- Modal Dialog -->
        <div class="border-[3px] border-[#012d1d] bg-[#f4fbf7] w-full max-w-5xl max-h-[85vh] flex flex-col shadow-[12px_12px_0px_0px_rgba(1,45,29,1)]"
             @click.stop
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Header -->
            <div class="flex justify-between items-center p-5 md:p-6 border-b-[3px] border-[#012d1d] bg-[#ffffff] shrink-0">
                <div>
                    <h3 class="font-headline font-black text-2xl text-[#012d1d] tracking-tight leading-none">Add New Product</h3>
                    <p class="font-body text-sm text-[#414844] mt-2">Fill in the details to add a new item.</p>
                </div>
                <button @click="showAddProduct = false" class="w-10 h-10 border-[3px] border-[#012d1d] bg-[#ffffff] flex items-center justify-center text-[#012d1d] hover:bg-[#ba1a1a] hover:text-white hover:border-[#ba1a1a] transition-colors shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] active:translate-y-0.5 active:translate-x-0.5 active:shadow-none">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Body (Scrollable Form) -->
            <div class="p-5 md:p-6 overflow-y-auto">
                <form novalidate onsubmit="event.preventDefault(); alert('Product Added! (Static View)'); showAddProduct = false;">
                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8 items-start">
                        
                        <!-- LEFT COLUMN: Info & Pricing -->
                        <div class="flex flex-col gap-6">
                            <!-- Product Info -->
                            <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
                                <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                                    <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Product Information</h3>
                                </div>
                                <div class="p-6 flex flex-col gap-5">
                                    <div class="group">
                                        <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Cake Name <span class="text-[#ba1a1a]">*</span></label>
                                        <input type="text" placeholder="Example: Premium Chocolate Brownie" class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#414844] placeholder:text-[#717973] focus:outline-none focus:bg-[#ffffff] focus:text-[#012d1d] focus:font-bold focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                                    </div>
                                    <div class="group">
                                        <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Description <span class="text-[#ba1a1a]">*</span></label>
                                        <textarea rows="4" placeholder="Describe the product..." class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 resize-y font-body text-sm text-[#414844] placeholder:text-[#717973] focus:outline-none focus:bg-[#ffffff] focus:text-[#012d1d] focus:font-bold focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300"></textarea>
                                    </div>
                                </div>
                            </section>
                            
                            <!-- Pricing & Weight -->
                            <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
                                <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                                    <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Pricing &amp; Weight</h3>
                                </div>
                                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div class="group">
                                        <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Price (Rp) <span class="text-[#ba1a1a]">*</span></label>
                                        <div class="flex border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                                            <span class="flex items-center px-3 bg-[#012d1d] text-[#d3ee6f] font-label font-bold text-sm border-r-[3px] border-[#012d1d]">Rp</span>
                                            <input type="number" placeholder="Example: 85000" class="flex-1 min-w-0 bg-transparent border-0 px-4 py-3 font-label text-right text-sm text-[#414844] focus:text-[#012d1d] focus:font-bold focus:outline-none transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Weight <span class="text-[#ba1a1a]">*</span></label>
                                        <div class="flex border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                                            <input type="number" placeholder="Example: 250" class="flex-1 min-w-0 bg-transparent border-0 px-4 py-3 font-label text-sm text-[#414844] focus:text-[#012d1d] focus:font-bold focus:outline-none transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                            <span class="flex items-center px-3 bg-[#012d1d] text-[#d3ee6f] font-label font-bold text-sm border-l-[3px] border-[#012d1d]">grams</span>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- RIGHT COLUMN: Photo, Status, Actions -->
                        <div class="flex flex-col gap-6">
                            <!-- Product Photo -->
                            <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
                                <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                                    <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Product Photo</h3>
                                </div>
                                <div class="p-5 flex flex-col gap-3">
                                    <div class="relative w-full aspect-square overflow-hidden cursor-pointer border-[3px] border-dashed border-[#012d1d] bg-[#eef5f1] hover:border-[#012d1d] hover:border-solid hover:bg-[#ffffff] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300" @click="$refs.imgInput.click()">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-4" x-show="!imagePreview">
                                            <div class="w-14 h-14 border-[3px] border-[#717973] bg-[#ffffff] flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]">
                                                <span class="material-symbols-outlined text-2xl text-[#717973]">image</span>
                                            </div>
                                            <div class="text-center">
                                                <p class="font-label font-bold text-[#012d1d] text-xs uppercase tracking-widest">Click to upload</p>
                                                <p class="font-body text-[#414844] text-xs mt-0.5">JPG, PNG, WebP — max 2MB</p>
                                            </div>
                                        </div>
                                        <img x-show="imagePreview" :src="imagePreview" class="absolute inset-0 w-full h-full object-cover border-[3px] border-[#012d1d]">
                                    </div>
                                    <input type="file" accept="image/*" class="hidden" x-ref="imgInput" @change="handleImage($event)">
                                    <button type="button" x-show="imagePreview" x-cloak @click.stop="imagePreview = null; $refs.imgInput.value = ''" class="w-full py-2.5 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d] font-label font-bold text-xs uppercase tracking-wider hover:bg-[#dde4e0] transition-colors flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-sm">delete</span> Remove Photo
                                    </button>
                                </div>
                            </section>

                            <!-- Product Status -->
                            <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
                                <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                                    <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Product Status</h3>
                                </div>
                                <div class="px-6 py-5 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-label font-bold text-sm text-[#012d1d] uppercase tracking-wide">Show Product</p>
                                        <p class="font-body text-xs text-[#414844] mt-0.5">
                                            <span x-show="isActive">Visible to customers</span>
                                            <span x-show="!isActive" x-cloak>Hidden from catalog</span>
                                        </p>
                                    </div>
                                    <button type="button" @click="isActive = !isActive" class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] transition-all duration-300 focus:outline-none cursor-pointer hover:scale-105" :style="isActive ? 'background:#d3ee6f;' : 'background:#dde4e0;'">
                                        <span class="absolute top-[2px] w-5 h-5 border-[3px] border-[#012d1d] transition-all duration-300 pointer-events-none" :style="isActive ? 'left:calc(100% - 1.375rem); background:#012d1d;' : 'left:1px; background:#717973;'"></span>
                                    </button>
                                </div>
                            </section>

                            <!-- Actions -->
                            <div class="flex flex-col gap-3">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-[#012d1d] text-white border-[3px] border-[#012d1d] font-label font-bold text-sm uppercase tracking-wider px-6 py-4 hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)] transition-all duration-200 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] active:translate-y-0 active:translate-x-0 active:shadow-none">
                                    <span class="material-symbols-outlined text-lg">save</span>
                                    Save Product
                                </button>
                                <button type="button" @click="showAddProduct = false" class="w-full flex items-center justify-center gap-2 bg-[#ffffff] text-[#ba1a1a] border-[3px] border-[#ba1a1a] font-label font-bold text-sm uppercase tracking-wider px-6 py-3.5 hover:bg-[#ba1a1a] hover:text-white transition-all duration-200 shadow-[4px_4px_0px_0px_#ba1a1a] hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_#ba1a1a] active:translate-y-0 active:translate-x-0 active:shadow-none">
                                    <span class="material-symbols-outlined text-base">close</span>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
