<?php if (get_setting($pdo, 'booking_mode', 'faces') === 'custom'): ?>
<!-- Booking Modal (Service Cart Flow) -->
<div id="bookingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4" onclick="closeBookingModal()">
    <div class="bg-bg w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden relative flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        <!-- Header -->
        <div class="p-6 pb-4 text-center border-b border-gray-200 relative shrink-0 bg-white">
            <button type="button" id="btnModalBack" onclick="goBackModal(); return false;" class="absolute top-4 left-4 w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-colors z-[1000] cursor-pointer hidden">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" onclick="closeBookingModal(); return false;" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-colors z-[1000] cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
            <h2 id="modalTitle" class="text-xl font-heading text-secondary pr-8 pl-8">Appointment Summary</h2>
        </div>

        <!-- View 1: Summary -->
        <div id="view-summary" class="p-8 overflow-y-auto bg-white flex-col flex h-full">
            <div id="cartItems" class="space-y-4 mb-6">
                <!-- Cart items injected here via JS -->
            </div>
            
            <button type="button" onclick="showAddView(); return false;" class="w-full border border-gray-300 text-gray-700 py-4 rounded-full flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors mb-6 font-light">
                <i class="fas fa-plus font-light"></i> Add Another Service
            </button>

            <div class="mt-auto">
                <button type="button" id="btnGoDetails" onclick="showDetailsView(); return false;" class="w-full bg-secondary text-white uppercase tracking-widest text-sm py-4 rounded-full hover:bg-opacity-90 transition-colors">Schedule Appointment</button>
            </div>
        </div>

        <!-- View 2: Add Service -->
        <div id="view-add" class="p-8 overflow-y-auto bg-white hidden flex-col h-full">
            <div class="mb-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-3 text-gray-400"></i>
                    <input type="text" id="serviceSearch" onkeyup="filterServices()" placeholder="Search" class="w-full border-b border-gray-300 pl-10 py-2 focus:outline-none focus:border-secondary font-light">
                </div>
            </div>
            <div id="serviceList" class="space-y-0 divide-y divide-gray-100">
                <!-- Service items injected here via JS -->
            </div>
        </div>

        <!-- View 3: Details & Checkout -->
        <div id="view-details" class="p-8 overflow-y-auto bg-white hidden flex-col h-full">
            <form id="bookingForm" class="space-y-5">
                <!-- Hidden input to store cart items -->
                <input type="hidden" name="cart_services" id="cartServicesInput">
                
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
                    <input type="text" name="name" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Email</label>
                        <input type="email" name="email" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Phone</label>
                        <input type="tel" name="phone" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Date</label>
                        <input type="date" name="date" min="<?= date('Y-m-d') ?>" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light text-gray-700" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Time</label>
                        <select name="time" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light text-gray-700" required>
                            <option value="Morning">Morning (9am - 12pm)</option>
                            <option value="Afternoon">Afternoon (12pm - 4pm)</option>
                            <option value="Evening">Evening (4pm - 7pm)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Skin Concerns (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" placeholder="E.g., breakouts, aging..."></textarea>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Payment Method</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="payment_method" value="later" checked class="accent-secondary">
                            <span class="text-sm text-gray-700">Pay Later (In Clinic)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="payment_method" value="paypal" class="accent-secondary">
                            <span class="text-sm text-gray-700">Pay Deposit (£20) via PayPal</span>
                        </label>
                    </div>
                </div>
                <div id="bookingMsg" class="hidden p-3 rounded text-sm text-center"></div>
                <button type="submit" id="bookingSubmit" class="w-full bg-secondary text-white uppercase tracking-widest text-sm py-4 rounded-full hover:bg-opacity-90 transition-colors mt-2">Submit Request</button>
            </form>
        </div>
    </div>
</div>

<script>
let shoppingCart = [];
let currentView = 'summary';

const originalOpenModal = window.openBookingModal;
window.openBookingModal = function(serviceName = null) {
    if (globalBookingMode === 'faces' && globalFacesUrl) {
        window.open(globalFacesUrl, '_blank');
        return false;
    }
    
    // Reset cart and add the clicked service
    shoppingCart = [];
    if(serviceName && typeof globalServicesList !== 'undefined') {
        const found = globalServicesList.find(s => s.title === serviceName);
        if(found) shoppingCart.push(found);
        else shoppingCart.push({title: serviceName, price: '£0', duration: '1 hr'});
    }
    
    renderCart();
    renderServiceList();
    showView('summary');
    
    document.getElementById('bookingModal').classList.remove('hidden');
    document.getElementById('bookingModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    const mobileMenu = document.getElementById('mobile-menu');
    if(mobileMenu) {
        mobileMenu.classList.add('hidden');
        mobileMenu.classList.remove('flex');
    }
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.remove('flex');
    document.getElementById('bookingModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function showView(viewId) {
    document.getElementById('view-summary').classList.add('hidden');
    document.getElementById('view-add').classList.add('hidden');
    document.getElementById('view-details').classList.add('hidden');
    document.getElementById('view-' + viewId).classList.remove('hidden');
    
    const btnBack = document.getElementById('btnModalBack');
    const title = document.getElementById('modalTitle');
    
    currentView = viewId;
    
    if(viewId === 'summary') {
        btnBack.classList.add('hidden');
        title.textContent = 'Appointment Summary';
    } else if(viewId === 'add') {
        btnBack.classList.remove('hidden');
        title.textContent = 'Add to your appointment';
    } else if(viewId === 'details') {
        btnBack.classList.remove('hidden');
        title.textContent = 'Your Details';
    }
}

function goBackModal() {
    if(currentView === 'add' || currentView === 'details') {
        showView('summary');
    }
}

function showAddView() {
    showView('add');
    document.getElementById('serviceSearch').value = '';
    filterServices();
}

function showDetailsView() {
    if(shoppingCart.length === 0) {
        alert("Please select at least one service.");
        return;
    }
    // populate hidden input
    const serviceTitles = shoppingCart.map(s => s.title).join(', ');
    document.getElementById('cartServicesInput').value = serviceTitles;
    showView('details');
}

function renderCart() {
    const container = document.getElementById('cartItems');
    if(shoppingCart.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-400 font-light italic">Your appointment is empty.</p>';
        document.getElementById('btnGoDetails').disabled = true;
        document.getElementById('btnGoDetails').classList.add('opacity-50');
        return;
    }
    document.getElementById('btnGoDetails').disabled = false;
    document.getElementById('btnGoDetails').classList.remove('opacity-50');
    
    let html = '';
    shoppingCart.forEach((item, index) => {
        html += `
        <div class="border-b border-gray-100 pb-4 relative">
            <h4 class="text-gray-800 font-light text-lg mb-1">${item.title}</h4>
            <p class="text-xs text-gray-400 mb-1">With any staff member</p>
            <div class="flex text-xs text-gray-500 font-light gap-2">
                <span>${item.duration || '1 hr'}</span>
                <span>•</span>
                <span>${item.price}</span>
            </div>
            <button type="button" onclick="removeFromCart(${index})" class="absolute right-0 bottom-4 text-xs text-gray-500 underline hover:text-red-500 transition-colors">Remove</button>
        </div>`;
    });
    container.innerHTML = html;
}

function renderServiceList(filter = '') {
    const container = document.getElementById('serviceList');
    if(typeof globalServicesList === 'undefined') return;
    
    let html = '';
    globalServicesList.forEach((item, index) => {
        if(filter && !item.title.toLowerCase().includes(filter.toLowerCase())) return;
        
        html += `
        <div class="py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center pr-2 group" onclick="addToCart(${index})">
            <div>
                <h4 class="text-gray-800 font-light text-[15px] mb-1 group-hover:text-accent transition-colors">${item.title}</h4>
                <div class="flex text-xs text-gray-400 font-light gap-2">
                    <span>${item.price}</span>
                    <span>•</span>
                    <span>${item.duration || '1 hr'}</span>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-sm"></i>
        </div>`;
    });
    container.innerHTML = html;
}

function addToCart(index) {
    if(typeof globalServicesList !== 'undefined') {
        shoppingCart.push(globalServicesList[index]);
        renderCart();
        showView('summary');
    }
}

function removeFromCart(index) {
    shoppingCart.splice(index, 1);
    renderCart();
}

function filterServices() {
    const q = document.getElementById('serviceSearch').value;
    renderServiceList(q);
}

// Handle Form Submission
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('bookingSubmit');
    const msg = document.getElementById('bookingMsg');
    
    btn.textContent = 'Submitting...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    // append service field for backend compatibility
    formData.append('service', document.getElementById('cartServicesInput').value);
    
    fetch('api/book.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        msg.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
        if (data.error) {
            msg.textContent = data.error;
            msg.classList.add('bg-red-100', 'text-red-700');
            btn.textContent = 'Submit Request';
            btn.disabled = false;
        } else {
            if (data.redirect) {
                msg.textContent = data.message;
                msg.classList.add('bg-green-100', 'text-green-700');
                window.location.href = data.redirect;
            } else {
                msg.textContent = data.message;
                msg.classList.add('bg-green-100', 'text-green-700');
                document.getElementById('bookingForm').reset();
                btn.textContent = 'Submit Request';
                btn.disabled = false;
                setTimeout(closeBookingModal, 3000);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        msg.classList.remove('hidden');
        msg.textContent = 'An error occurred. Please try again.';
        msg.classList.add('bg-red-100', 'text-red-700');
        btn.textContent = 'Submit Request';
        btn.disabled = false;
    });
});
</script>

<!-- Faces Consent Iframe Modal -->
<div id="facesModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4 md:p-8" onclick="closeFacesModal()">
    <div class="bg-bg w-full max-w-5xl h-[90vh] md:h-[85vh] rounded-2xl shadow-2xl overflow-hidden relative flex flex-col" onclick="event.stopPropagation()">
        <div class="p-4 border-b border-gray-200 relative shrink-0 flex justify-between items-center bg-white">
            <h2 class="text-xl font-heading text-secondary">Book Your Appointment</h2>
            <button type="button" onclick="closeFacesModal(); return false;" class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-colors cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex-grow w-full h-full bg-white relative">
            <div id="facesLoader" class="absolute inset-0 flex items-center justify-center bg-white">
                <i class="fas fa-spinner fa-spin text-3xl text-accent"></i>
            </div>
            <iframe id="facesIframe" src="" class="w-full h-full border-0 relative z-10" style="pointer-events: auto !important;" onload="document.getElementById('facesLoader').style.display='none'"></iframe>
        </div>
    </div>
</div>
<script>
    function closeFacesModal() {
        document.getElementById('facesModal').classList.remove('flex');
        document.getElementById('facesModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('facesIframe').src = ''; // clear iframe
    }
</script>
<?php endif; ?>