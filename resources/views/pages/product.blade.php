@extends('master')
@section('main')
    <section class="flex-1 overflow-y-auto pt-4">
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
        <div class="mt-10 pl-5 overflow-y-auto pb-10">
            @foreach ($product as $item)
                <div class="mb-8">
                    <p class="text-sm font-bold mb-4 uppercase text-red-900">{{$item->title}}</p>
                    <div id="scroll-container" class="overflow-x-auto whitespace-nowrap scroll-hidden cursor-grab">
                        @foreach ($item->product as $product)
                            <div class="inline-block mr-2 h-fit align-top mb-auto ml-1 my-1 rounded max-w-40 truncate cursor-pointer" 
                                onclick="@if ($product->m_status_tabs_id != 3) addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}) @endif">
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
    <div id="quantityModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg w-80 shadow-lg">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Jumlah Item</h2>
            <input id="qtyInput" type="number" min="1" value="1"
                class="w-full border border-gray-300 rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-red-500" />
            <div class="flex justify-end gap-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Batal</button>
                <button onclick="confirmAddToCart()" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Tambah</button>
            </div>
        </div>
    </div>
    <button onclick="openCartBox()" id="openCartBtn"
        class="bg-red-600 text-white px-4 py-2 w-fit mx-auto mb-2 rounded-full shadow-lg hover:bg-red-700">
        Keranjang ( <span id="cart-count">0</span> )
    </button>
    <div id="cartBox" class="fixed left-1/2 -translate-x-1/2 w-full bottom-0 max-w-md z-10 p-4 border rounded-t-lg bg-white shadow-lg hidden">
        <div class="flex justify-between items-center mb-2">
            <h2 class="font-bold text-red-700">Keranjang Anda</h2>
            <button onclick="toggleCartBox()" class="text-gray-500 hover:text-red-600 text-sm">Tutup ✖</button>
        </div>
        <ul id="cart-list" class="text-sm text-gray-700 space-y-1">
            <!-- akan diisi oleh JavaScript -->
        </ul>
        <!-- Total Harga -->
        <p id="total-harga" class="text-right font-semibold text-sm mt-3 hidden">
            Total: Rp <span id="total-bayar">0</span>
        </p>

        <!-- Form Checkout -->
        <form id="checkoutForm" method="POST" action="{{ route('checkout', $tableNo) }}" class="mt-3 hidden">
            @csrf
            <input type="hidden" name="cart" id="cartInput" />
            <div class="mb-2">
                <label for="nama" class="block text-sm font-medium">Nama <span class="text-red-600">*</span></label>
                <input type="text" id="nama" name="nama" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 text-sm focus:ring-red-500 focus:outline-none" />
            </div>
            <div class="mb-2">
                <label for="no_hp" class="block text-sm font-medium">Nomor HP</label>
                <input type="number" id="no_hp" name="no_hp"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 text-sm focus:ring-red-500 focus:outline-none" />
            </div>
            <div class="mb-2">
                <label for="notes" class="block text-sm font-medium">Notes Pesanan</label>
                <input type="textarea" id="notes" name="notes"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 text-sm focus:ring-red-500 focus:outline-none" />
            </div>
            <button type="submit" id="checkoutBtn"
                class=" text-sm text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded hidden">
                Checkout & Bayar
            </button>
        </form>
    </div>
    <script>
        const slider = document.getElementById('scroll-container');
        let isDown = false;
        let startX;
        let scrollLeft;
        let cart = [];
        let currentItem = null;
    
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

        function addToCart(id, name, price) {
            currentItem = { id, name, price };
            document.getElementById('qtyInput').value = 1;
            document.getElementById('quantityModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('quantityModal').classList.add('hidden');
        }

        function confirmAddToCart() {
            const qty = parseInt(document.getElementById('qtyInput').value);
            if (isNaN(qty) || qty < 1) {
                alert("Jumlah harus minimal 1");
                return;
            }

            const existing = cart.find(item => item.id === currentItem.id);
            if (existing) {
                existing.qty += qty;
            } else {
                cart.push({ ...currentItem, qty });
            }

            closeModal();
            renderCart();
            updateCartInput();
        }

        function renderCart() {
            const cartList = document.getElementById('cart-list');
            const checkoutForm = document.getElementById('checkoutForm');
            const totalBayarText = document.getElementById('total-harga');
            const totalBayar = document.getElementById('total-bayar');
            const cartBtn = document.getElementById('openCartBtn');
            const cartCount = document.getElementById('cart-count');
            if (!cartList) return;

            cartList.innerHTML = '';
            let total = 0;
            let totalQty = 0;
            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = "flex justify-between items-center";

                li.innerHTML = `
                <span>${item.name} x ${item.qty} - Rp ${item.price * item.qty}</span>
                <button onclick="removeCartItem(${index})" class="text-red-600 hover:text-red-800 ml-2 text-xs">
                    ❌
                </button>
                `;
                cartList.appendChild(li);
                total += item.price * item.qty;
                totalQty += item.qty;
            });
            cartCount.textContent = totalQty;
            totalBayar.textContent = total;
            updateCartInput();

            // Tampilkan tombol hapus semua jika ada item
            const hasItem = cart.length > 0;
            checkoutForm.classList.toggle('hidden', !hasItem);
            totalBayarText.classList.toggle('hidden', !hasItem);
            // cartBtn.classList.toggle('hidden', cart.length === 0);


            document.getElementById('checkoutBtn').classList.toggle('hidden', cart.length === 0);
        }

        function removeCartItem(index) {
            cart.splice(index, 1);
            if(cart.length < 1) toggleCartBox()
            renderCart();
        }

        function clearCart() {
            if (confirm('Yakin ingin menghapus semua item di keranjang?')) {
                cart.length = 0;
                renderCart();
            }
        }

        function updateCartInput() {
            const cartInput = document.getElementById('cartInput');
            if (cartInput) {
                cartInput.value = JSON.stringify(cart);
            }
        }

        function toggleCartBox() {
            const box = document.getElementById('cartBox');
            box.classList.toggle('hidden');
        }
        
        function openCartBox(){
            const box = document.getElementById('cartBox');
            if(cart.length > 0){
                box.classList.toggle('hidden');
            }
        }
    </script>
@endsection