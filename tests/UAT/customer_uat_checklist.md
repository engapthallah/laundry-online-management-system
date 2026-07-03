# Customer UAT Checklist

**Tester:** ___________
**Date:** ___________
**Browser:** ___________
**URL:** http://127.0.0.1:8000

## 1. Authentication
- [ ] Visit `/register` — form loads correctly
- [ ] Register with valid data — success, redirect
- [ ] Register with duplicate email — error shown
- [ ] Register with weak password — error shown
- [ ] Visit `/login` — form loads
- [ ] Login with wrong password — error shown
- [ ] Login with correct credentials — redirect to home page (`/`)
- [ ] Logout — redirect to home page

## 2. Customer Landing Page & Dashboard Removal
- [ ] Direct navigation to `/customer/dashboard` returns a 404 page
- [ ] Nav bar "Welcome back" dropdown contains no "Dashboard" option
- [ ] Navigation to `/` (home page) displays "Our Track Record" correctly
- [ ] Customer can navigate to My Orders, Support, Profile, and Logout from the dropdown

## 3. Place New Order
- [ ] Visit `/customer/orders/create` — form loads
- [ ] Active services shown as cards
- [ ] Select a service — card highlights
- [ ] Enter quantity and weight — price calculates
- [ ] Running total updates correctly
- [ ] Fill pickup address (pre-filled from profile)
- [ ] Fill delivery address
- [ ] Set pickup date (tomorrow or later)
- [ ] Set delivery date (after pickup)
- [ ] Select "Cash on Delivery" — correct card shown
- [ ] Select "Zaad" — phone input appears
- [ ] Select "Edahab" — phone input appears
- [ ] Review order summary on Step 4
- [ ] Click "Confirm Order" — order created
- [ ] Success message shown
- [ ] Redirect to order detail page
- [ ] Order number shown (LOMS-YYYYMMDD-XXXX)

## 4. Order Tracking
- [ ] Visit `/customer/orders` — orders listed
- [ ] Filter by status — works correctly
- [ ] Search by order number — works
- [ ] Click "View" — order detail loads
- [ ] Status tracker shows current step highlighted
- [ ] Order items table shows services correctly
- [ ] Payment information shown correctly
- [ ] "Cancel Order" button visible (if pending)
- [ ] Cancel confirmation dialog appears
- [ ] Order cancelled successfully

## 5. Payment
- [ ] Zaad payment: instructions page shown
- [ ] Merchant number visible and copyable
- [ ] Reference number visible and copyable
- [ ] Countdown timer starts from 30:00
- [ ] Confirm button shows JavaScript dialog
- [ ] Confirm payment — success, transaction ref created
- [ ] Payment history page loads correctly
- [ ] Filter payments by method — works
- [ ] Receipt page loads and is printable

## 6. Reviews
- [ ] Visit `/customer/reviews` — page loads
- [ ] Delivered orders show "Leave Review" prompt
- [ ] Review form loads with order summary
- [ ] Star hover effect works (stars fill)
- [ ] Click star — rating locked, label shown
- [ ] Submit without star — error shown
- [ ] Submit valid review — success message
- [ ] Review appears in My Reviews page
- [ ] Cannot review same order twice

## 7. Support Messages
- [ ] Visit `/customer/support` — page loads
- [ ] Click "New Message" — form loads
- [ ] Name and email pre-filled (readonly)
- [ ] Quick-select subject buttons work
- [ ] Submit message — success message shown
- [ ] Message appears in support list
- [ ] Status shows "Awaiting Reply"
- [ ] View message — chat layout shown correctly
- [ ] After admin reply: reply bubble visible

## 8. Profile
- [ ] Visit profile edit page
- [ ] Current data pre-filled
- [ ] Update name — saved correctly
- [ ] Update phone — saved correctly
- [ ] Wrong current password — error shown
- [ ] Correct current password — password changed
- [ ] Success message shown after update
