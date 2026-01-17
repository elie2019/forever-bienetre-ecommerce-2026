<?php
/**
 * Template: FitTrack Pricing
 */

get_header();

$stripe = FitTrack_Stripe::get_instance();
$plans = $stripe->get_plans();
?>

<div class="fittrack-pricing min-h-screen bg-gradient-to-b from-gray-50 to-white py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-900 mb-4">Choose Your Plan</h1>
            <p class="text-xl text-gray-600">Unlock your full fitness potential</p>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Free Plan -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Free</h3>
                    <div class="text-4xl font-bold text-gray-900">
                        €0
                        <span class="text-lg font-normal text-gray-500">/month</span>
                    </div>
                </div>
                <ul class="space-y-3 mb-8">
                    <?php foreach($plans['free']['features'] as $feature): ?>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-600"><?php echo $feature; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <button class="w-full bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg cursor-not-allowed">
                    Current Plan
                </button>
            </div>

            <!-- Pro Plan -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border-4 border-teal-500 relative transform scale-105">
                <div class="absolute top-0 right-0 bg-teal-500 text-white px-4 py-1 rounded-bl-lg rounded-tr-lg text-sm font-bold">
                    POPULAR
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro</h3>
                    <div class="text-4xl font-bold text-teal-600">
                        €9.99
                        <span class="text-lg font-normal text-gray-500">/month</span>
                    </div>
                </div>
                <ul class="space-y-3 mb-8">
                    <?php foreach($plans['pro']['features'] as $feature): ?>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-600"><?php echo $feature; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="subscribeToPlan('pro')" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105">
                    Subscribe Now
                </button>
            </div>

            <!-- Premium Plan -->
            <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-8 text-white">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold mb-2">Premium</h3>
                    <div class="text-4xl font-bold">
                        €79.99
                        <span class="text-lg font-normal opacity-90">/year</span>
                    </div>
                    <p class="text-sm opacity-75 mt-1">Save 33% vs monthly</p>
                </div>
                <ul class="space-y-3 mb-8">
                    <?php foreach($plans['premium']['features'] as $feature): ?>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-300 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="opacity-90"><?php echo $feature; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="subscribeToPlan('premium')" class="w-full bg-white text-purple-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition transform hover:scale-105">
                    Subscribe Now
                </button>
            </div>
        </div>

        <!-- FAQ -->
        <div class="mt-16 max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-8">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <details class="bg-white rounded-lg shadow p-6">
                    <summary class="font-bold text-gray-900 cursor-pointer">Can I cancel anytime?</summary>
                    <p class="mt-2 text-gray-600">Yes! You can cancel your subscription at any time. You'll continue to have access until the end of your billing period.</p>
                </details>
                <details class="bg-white rounded-lg shadow p-6">
                    <summary class="font-bold text-gray-900 cursor-pointer">What payment methods do you accept?</summary>
                    <p class="mt-2 text-gray-600">We accept all major credit cards (Visa, Mastercard, American Express) through Stripe's secure payment processing.</p>
                </details>
                <details class="bg-white rounded-lg shadow p-6">
                    <summary class="font-bold text-gray-900 cursor-pointer">Can I upgrade or downgrade later?</summary>
                    <p class="mt-2 text-gray-600">Absolutely! You can change your plan at any time from your account settings.</p>
                </details>
            </div>
        </div>
    </div>
</div>

<!-- Ensure jQuery is loaded -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
// FitTrack Data Configuration
const fittrackData = {
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('fittrack_nonce'); ?>',
    isLoggedIn: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
    userId: <?php echo get_current_user_id(); ?>
};

// Initialize Stripe
const stripePublishableKey = '<?php echo esc_js($stripe->get_publishable_key()); ?>';
if (!stripePublishableKey) {
    console.error('Stripe publishable key not configured. Please add FITTRACK_STRIPE_PUBLISHABLE_KEY to wp-config.php');
}
const stripe = stripePublishableKey ? Stripe(stripePublishableKey) : null;

function subscribeToPlan(plan) {
    // Check if user is logged in
    if (!fittrackData.isLoggedIn) {
        alert('Please log in to subscribe');
        window.location.href = '<?php echo wp_login_url(get_permalink()); ?>';
        return;
    }

    // Check if Stripe is initialized
    if (!stripe) {
        alert('Payment system not configured. Please contact support.');
        return;
    }

    // Show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Processing...';
    button.disabled = true;

    // Create checkout session
    jQuery.ajax({
        url: fittrackData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'fittrack_create_checkout_session',
            plan: plan,
            nonce: fittrackData.nonce
        },
        success: function(response) {
            if (response.success) {
                // Redirect to Stripe Checkout
                stripe.redirectToCheckout({ sessionId: response.data.sessionId })
                    .then(function(result) {
                        if (result.error) {
                            alert('Error: ' + result.error.message);
                            button.textContent = originalText;
                            button.disabled = false;
                        }
                    });
            } else {
                alert('Error: ' + (response.data.message || 'An error occurred'));
                button.textContent = originalText;
                button.disabled = false;
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error, xhr.responseText);
            alert('An error occurred. Please try again. Check console for details.');
            button.textContent = originalText;
            button.disabled = false;
        }
    });
}
</script>

<?php get_footer(); ?>
