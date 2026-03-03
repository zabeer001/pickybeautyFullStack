# Payment Step Documentation

This folder contains the frontend payment step and the Stripe Checkout return pages.

## Files

- `PaymentStepPage.jsx`
  - Thin page wrapper that renders the main payment component.

- `_components/PaymentStep.jsx`
  - Main payment-step UI.
  - Lets the user choose `cash` or `online`.
  - Loads discounted budget from the backend.
  - Creates the order before starting Stripe Checkout.
  - Starts Stripe Checkout only when `paymentMethod === "online"`.

- `PaymentSuccessPage.jsx`
  - Dedicated success page shown after Stripe redirects to the configured `success_url`.
  - Resets the order store on load.

- `PaymentFailurePage.jsx`
  - Dedicated failure page shown after Stripe redirects to the configured `cancel_url`.

- `_components/PaymentStatusPage.jsx`
  - Shared status-card UI used by the success and failure pages.

## Current Payment Flow

### 1. User opens the payment step

The user reaches the payment page at:

- `#/payment`

The main component reads form state from the Zustand stores:

- selected category
- zipcode
- budget
- order details
- chosen payment method

### 2. Discount is requested

When the page loads, the frontend calls:

- `POST {WP_BACKEND}/wp-json/kibsterlp-admin/v1/order-discount`

Payload:

```json
{
  "email": "customer@example.com",
  "budget": 100
}
```

If the backend returns a discounted value, that amount is shown in the payment UI.

### 3. User submits the order

When the user clicks submit, the frontend always creates the order first:

- `POST {WP_BACKEND}/wp-json/kibsterlp-admin/v1/orders`

Payload includes:

- `category_id`
- `budget`
- `payment_method`
- `x`
- `y`
- `shipping`

This means the order is created in the backend before any Stripe redirect happens.

## Cash Payment Flow

If the selected payment method is `cash`:

1. The order is created.
2. The order store is reset.
3. A success toast is shown.
4. The user is redirected to `#/`.

## Online Stripe Payment Flow

If the selected payment method is `online`:

1. The order is created first.
2. The displayed amount is converted to Stripe cents.
   - Example: `49.99` becomes `4999`
3. The frontend calls:
   - `POST {WP_BACKEND}/wp-json/kibsterlp-admin/v1/stripe/checkout`
4. The request body includes:
   - `amount`
   - `currency`
   - `order_id`
   - `email`
   - `success_url`
   - `cancel_url`
5. The backend returns a Stripe-hosted Checkout URL.
6. The frontend redirects the browser with:
   - `window.location.assign(checkoutData.url)`

## Stripe Return URLs

The frontend currently uses dedicated hash routes for Stripe return handling:

- Success URL:
  - `#/payment-success?session_id={CHECKOUT_SESSION_ID}`

- Failure / cancel URL:
  - `#/payment-failed?checkout=cancelled`

These URLs are built inside `_components/PaymentStep.jsx`.

## What Success Means Right Now

The success page is shown when Stripe redirects the browser to the configured `success_url`.

In practice, for Stripe Checkout, that means Checkout completed successfully.

The success page currently does not call the backend again to verify the session. It relies on Stripe's redirect behavior.

Current success page message:

- `Die Zahlung wurde erfolgreich abgeschlossen.`

## What Failure Means Right Now

The failure page is shown when Stripe redirects the browser to the configured `cancel_url`.

That normally means the user cancelled or exited the Checkout flow.

Current failure page message:

- `Die Zahlung ist fehlgeschlagen.`

## Important Stripe Behavior

Stripe Checkout does not usually redirect to the failure page for every payment error.

For example, if the card is declined because of insufficient funds:

1. Stripe usually keeps the customer on the Stripe Checkout page.
2. Stripe shows the error there.
3. The customer can retry another card or cancel.

So:

- a declined card should not go to the success page
- a declined card may not automatically go to the failure page either
- the failure page is mainly tied to the Checkout cancel flow

## Current Limitation

The frontend is not currently verifying payment status against backend data after Stripe returns.

It is using Stripe's redirect destination only:

- `success_url` -> success page
- `cancel_url` -> failure page

If strict backend confirmation is needed, the success page should call:

- `GET {WP_BACKEND}/wp-json/kibsterlp-admin/v1/stripe/checkout-status?session_id=...`

Then it should only keep the user on the success page if the backend confirms:

- `payment_status === "paid"`

Otherwise it should move the user to the failure page.

## No Extra Frontend Package

No Stripe npm package was added for this flow.

The frontend uses:

- normal `fetch`
- existing React Router routes
- existing Zustand state
- Stripe-hosted Checkout via backend-generated session URL

## Summary

- `cash` payment finishes entirely inside the app
- `online` payment uses Stripe-hosted Checkout
- success page is `#/payment-success`
- failure page is `#/payment-failed`
- order is created before Stripe redirect
- frontend currently trusts Stripe's redirect route instead of re-checking backend payment status
