<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4" onclick="closeBookingModal()">
    <div class="bg-bg w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden relative flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        <div class="p-6 pb-4 text-center border-b border-gray-200 relative shrink-0">
            <button type="button" onclick="closeBookingModal(); return false;" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-colors z-[1000] cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="text-2xl font-heading text-secondary pr-8">Request an Appointment</h2>
            <p class="text-sm text-gray-500 font-light mt-2">Begin your transformation journey</p>
        </div>
        <div class="p-8 overflow-y-auto" data-lenis-prevent>
            <form id="bookingForm" class="space-y-5">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
                    <input type="text" name="name" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
                        <input type="email" name="email" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Phone Number</label>
                        <input type="tel" name="phone" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Service</label>
                    <select name="service" id="bookingService" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light text-gray-700" required>
                        <option value="">Select a treatment</option>
                        <option value="Initial Consultation">Initial Skin Consultation</option>
                        <?php foreach ($treatments as $t): ?>
                            <option value="<?= htmlspecialchars($t['title']) ?>"><?= htmlspecialchars($t['title']) ?></option>
                        <?php endforeach; ?>
                        <option value="Not Sure yet">I'm not sure yet</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Preferred Date</label>
                        <input type="date" name="date" min="<?= date('Y-m-d') ?>" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light text-gray-700" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Preferred Time</label>
                        <select name="time" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light text-gray-700" required>
                            <option value="Morning">Morning (9am - 12pm)</option>
                            <option value="Afternoon">Afternoon (12pm - 4pm)</option>
                            <option value="Evening">Evening (4pm - 7pm)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Skin Concerns (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-white border border-gray-200 px-4 py-3 focus:outline-none focus:border-accent font-light" placeholder="E.g., breakouts, aging, redness..."></textarea>
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
                            <span class="text-sm text-gray-700">Pay Now (PayPal)</span>
                        </label>
                    </div>
                </div>
                <div id="bookingMsg" class="hidden p-3 rounded text-sm text-center"></div>
                
                <button type="submit" id="bookingSubmit" class="w-full bg-secondary text-white uppercase tracking-widest text-sm py-4 hover:bg-opacity-90 transition-colors mt-2">Submit Request</button>
            </form>
        </div>
    </div>
</div>

<script>
// Attach service if provided via button click
const originalOpenModal = window.openBookingModal;
window.openBookingModal = function(serviceName = null) {
    if(originalOpenModal) originalOpenModal();
    if(serviceName) {
        const select = document.getElementById('bookingService');
        for(let i=0; i<select.options.length; i++) {
            if(select.options[i].value === serviceName) {
                select.selectedIndex = i;
                break;
            }
        }
    }
}

// Handle Form Submission
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('bookingSubmit');
    const msg = document.getElementById('bookingMsg');
    
    btn.textContent = 'Submitting...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    
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
