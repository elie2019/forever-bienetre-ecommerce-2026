<?php
/**
 * Stripe Checkout Session Creator
 * Forever Bien-Etre Landing Page
 */

// CORS headers for AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load Stripe Secret Key from environment or config file
$stripe_secret_key = '';

// Method 1: Check environment variable
if (getenv('STRIPE_SECRET_KEY')) {
    $stripe_secret_key = getenv('STRIPE_SECRET_KEY');
}
// Method 2: Check .env file in same directory
elseif (file_exists(__DIR__ . '/.env')) {
    $env_content = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/STRIPE_SECRET_KEY=(.+)/', $env_content, $matches)) {
        $stripe_secret_key = trim($matches[1]);
    }
}

// Validate key exists
if (empty($stripe_secret_key)) {
    http_response_code(500);
    echo json_encode(['error' => 'Stripe configuration missing. Please set STRIPE_SECRET_KEY in .env file.']);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

$product_name = isset($input['name']) ? sanitize($input['name']) : 'Produit Forever Bien-Etre';
$price = isset($input['price']) ? intval($input['price']) : 0;

if ($price <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid price']);
    exit();
}

// Base URL for redirects
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$theme_url = $base_url . '/wp-content/themes/forever-be-wp-premium';

// Create Stripe Checkout Session using cURL
$checkout_data = [
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $product_name,
                'description' => 'Produit premium Forever Bien-Etre',
            ],
            'unit_amount' => $price,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $theme_url . '/shop-landing.html?success=true&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $theme_url . '/shop-landing.html?canceled=true',
    'billing_address_collection' => 'required',
    'shipping_address_collection' => [
        'allowed_countries' => ['FR', 'BE', 'CH', 'CA', 'LU', 'MC'],
    ],
];

// Make API call to Stripe
$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $stripe_secret_key,
    'Content-Type: application/x-www-form-urlencoded',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query_nested($checkout_data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($http_code !== 200) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Stripe API error',
        'message' => isset($result['error']['message']) ? $result['error']['message'] : 'Unknown error'
    ]);
    exit();
}

// Return session ID and URL
echo json_encode([
    'sessionId' => $result['id'],
    'url' => $result['url']
]);

/**
 * Sanitize input string
 */
function sanitize($str) {
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
}

/**
 * Build nested query string for Stripe API
 */
function http_build_query_nested($data, $prefix = '') {
    $result = [];

    foreach ($data as $key => $value) {
        $new_key = $prefix ? "{$prefix}[{$key}]" : $key;

        if (is_array($value)) {
            $result[] = http_build_query_nested($value, $new_key);
        } else {
            $result[] = urlencode($new_key) . '=' . urlencode($value);
        }
    }

    return implode('&', $result);
}
