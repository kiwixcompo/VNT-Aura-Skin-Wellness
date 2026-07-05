<?php
require_once __DIR__ . '/includes/db.php';

$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    die("Invalid booking reference.");
}

$stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Booking not found.");
}

// Fetch logo
$siteLogo = get_setting($pdo, 'site_logo', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Intake - VNT Aura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#fcfcfc',
                        text: '#1a1c1a',
                        accent: '#c4a68a',
                        secondary: '#2c332e',
                        faces: '#1c64f2' // The blue from Faces Consent
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f5f6f8; }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 40px;
        }
        .faces-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.2s;
        }
        .faces-input:focus {
            outline: none;
            border-color: #1c64f2;
            box-shadow: 0 0 0 3px rgba(28, 100, 242, 0.1);
        }
        .faces-label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .step { display: none; }
        .step.active { display: block; }
    </style>
</head>
<body class="text-gray-800">

    <div class="form-container">
        
        <div class="flex items-center gap-4 border border-gray-200 rounded-xl p-4 mb-8">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                <?php if ($siteLogo): ?>
                    <img src="<?= htmlspecialchars($siteLogo) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-clinic-medical"></i></div>
                <?php endif; ?>
            </div>
            <div class="font-semibold">VNT Aura skin and wellness</div>
        </div>

        <form id="intakeForm" action="api/save_intake.php" method="POST">
            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking_id) ?>">

            <!-- STEP 1: Details -->
            <div id="step1" class="step active">
                <h1 class="text-2xl font-bold mb-2">Confirm your details</h1>
                <p class="text-sm text-gray-500 mb-6">Please ensure your personal information is up to date.</p>
                
                <div class="bg-gray-50 rounded-xl p-4 mb-8 text-sm leading-relaxed text-gray-700">
                    You have an appointment for <strong><?= htmlspecialchars($booking['service']) ?></strong> at <strong>VNT Aura Skin + Wellness</strong> on <strong><?= date('l, d F', strtotime($booking['preferred_date'])) ?></strong> at <strong><?= htmlspecialchars($booking['preferred_time']) ?></strong>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="faces-label">Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($booking['client_name']) ?>" class="faces-input" required>
                    </div>

                    <div>
                        <label class="faces-label">Date of birth</label>
                        <div class="grid grid-cols-3 gap-4">
                            <input type="text" name="dob_day" placeholder="DD" maxlength="2" class="faces-input text-center" required>
                            <input type="text" name="dob_month" placeholder="MM" maxlength="2" class="faces-input text-center" required>
                            <input type="text" name="dob_year" placeholder="YYYY" maxlength="4" class="faces-input text-center" required>
                        </div>
                    </div>

                    <div>
                        <label class="faces-label">Email address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($booking['client_email']) ?>" class="faces-input" required>
                    </div>

                    <div>
                        <label class="faces-label">Phone Number</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($booking['client_phone']) ?>" class="faces-input" required>
                    </div>

                    <div>
                        <label class="faces-label">Address</label>
                        <textarea name="address" rows="3" class="faces-input" placeholder="Enter your full address..." required></textarea>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="button" onclick="nextStep()" class="w-full bg-faces text-white font-medium py-3 rounded-full hover:bg-blue-700 transition-colors">Confirm & Continue</button>
                </div>
            </div>

            <!-- STEP 2: Medical Form -->
            <div id="step2" class="step">
                <h1 class="text-2xl font-bold mb-2">Medical form</h1>
                <p class="text-sm text-gray-500 mb-6">Please complete the medical form below</p>

                <div class="space-y-6">
                    <div>
                        <label class="faces-label">Gender</label>
                        <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                            <label class="flex-1 flex items-center justify-center gap-2 py-3 border-r border-gray-300 cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="gender" value="Male" class="accent-faces" required> Male
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 py-3 border-r border-gray-300 cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="gender" value="Female" class="accent-faces"> Female
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 py-3 cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="gender" value="Other" class="accent-faces"> Other
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="faces-label">Height (cm) (Optional)</label>
                            <input type="text" name="height" class="faces-input">
                        </div>
                        <div>
                            <label class="faces-label">Weight (kg) (Optional)</label>
                            <input type="text" name="weight" class="faces-input">
                        </div>
                    </div>

                    <div>
                        <label class="faces-label flex items-center gap-2"><i class="fas fa-user-md text-orange-500"></i> General practitioners (GP / Doctors)</label>
                        <textarea name="gp_details" rows="2" class="faces-input" placeholder="GP Name and Clinic..."></textarea>
                    </div>

                    <div>
                        <label class="faces-label">Do you have any known health conditions or medical history?</label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center justify-between border border-gray-300 rounded-lg p-3 cursor-pointer hover:bg-gray-50">
                                Yes <input type="radio" name="has_medical_conditions" value="Yes" onchange="toggleMed(true)" class="accent-faces" required>
                            </label>
                            <label class="flex-1 flex items-center justify-between border border-gray-300 rounded-lg p-3 cursor-pointer hover:bg-gray-50">
                                No <input type="radio" name="has_medical_conditions" value="No" onchange="toggleMed(false)" class="accent-faces">
                            </label>
                        </div>
                    </div>

                    <div id="medHistoryBox" class="hidden">
                        <label class="faces-label">Please provide details</label>
                        <textarea name="medical_history" rows="3" class="faces-input"></textarea>
                    </div>

                    <div class="border border-gray-300 rounded-lg p-4 flex gap-3 items-start mt-4">
                        <input type="checkbox" name="confirm_accurate" class="mt-1 accent-faces" required>
                        <span class="text-sm text-gray-700">I confirm the above medical information is accurate to the best of my knowledge and accept responsibility for any errors.</span>
                    </div>

                    <div class="border border-gray-300 border-dashed rounded-lg p-6 text-center bg-gray-50">
                        <i class="fas fa-signature text-2xl text-gray-400 mb-2"></i>
                        <div class="text-xs text-gray-500 mb-3">Patient signature (Type Full Name)</div>
                        <input type="text" name="signature_name" class="faces-input text-center font-heading italic text-xl" required>
                    </div>

                </div>

                <div id="formMsg" class="hidden mt-6 p-4 rounded-lg text-sm text-center"></div>

                <div class="mt-8 flex gap-4">
                    <button type="button" onclick="prevStep()" class="w-1/3 bg-gray-200 text-gray-800 font-medium py-3 rounded-full hover:bg-gray-300 transition-colors">Back</button>
                    <button type="submit" id="submitBtn" class="w-2/3 bg-faces text-white font-medium py-3 rounded-full hover:bg-blue-700 transition-colors">Submit & Complete Booking</button>
                </div>
            </div>
            
            <div id="stepSuccess" class="step text-center py-10">
                <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
                    <i class="fas fa-check"></i>
                </div>
                <h2 class="text-2xl font-bold mb-4">Booking Complete!</h2>
                <p class="text-gray-500 mb-8">Your appointment is confirmed and your intake forms have been securely saved. You will receive a confirmation email shortly.</p>
                <a href="index.php" class="inline-block bg-secondary text-white px-8 py-3 rounded-full font-medium hover:bg-opacity-90 transition-colors">Return to Homepage</a>
            </div>

        </form>
    </div>

    <script>
        function nextStep() {
            // Very basic validation check for step 1 required fields
            const step1 = document.getElementById('step1');
            const required = step1.querySelectorAll('[required]');
            for(let r of required) {
                if(!r.value) {
                    r.focus();
                    return;
                }
            }
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');
        }

        function prevStep() {
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step1').classList.add('active');
        }

        function toggleMed(show) {
            const box = document.getElementById('medHistoryBox');
            if(show) {
                box.classList.remove('hidden');
                box.querySelector('textarea').required = true;
            } else {
                box.classList.add('hidden');
                box.querySelector('textarea').required = false;
                box.querySelector('textarea').value = '';
            }
        }

        document.getElementById('intakeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const msg = document.getElementById('formMsg');
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            btn.disabled = true;
            msg.classList.add('hidden');

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('step2').classList.remove('active');
                    document.getElementById('stepSuccess').classList.add('active');
                } else {
                    msg.textContent = data.error || 'An error occurred. Please try again.';
                    msg.className = 'mt-6 p-4 rounded-lg text-sm text-center bg-red-100 text-red-700 block';
                    btn.textContent = 'Submit & Complete Booking';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                msg.textContent = 'A network error occurred. Please try again.';
                msg.className = 'mt-6 p-4 rounded-lg text-sm text-center bg-red-100 text-red-700 block';
                btn.textContent = 'Submit & Complete Booking';
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
