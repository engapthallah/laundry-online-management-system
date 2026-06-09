# Delivery Agent UAT Checklist

**Tester:** ___________
**Date:** ___________
**Browser:** ___________
**URL:** http://127.0.0.1:8000

## 1. Authentication
- [ ] Login as delivery@loms.com redirects to `/delivery/dashboard`
- [ ] Cannot access `/admin`, `/customer`, `/staff` paths (blocked or redirected)

## 2. Delivery Dashboard
- [ ] Stat cards show correct counts (Assigned, Picked Up, On the Way, Delivered)
- [ ] Active delivery cards shown in assignments listing
- [ ] Payment method badge correct (red "COLLECT CASH" or green "ALREADY PAID")
- [ ] Urgency borders shown for near-deadline deliveries
- [ ] Donut chart renders correctly

## 3. Delivery Processing
- [ ] My Deliveries shows only agent's own assignments
- [ ] Active and Completed tabs work correctly
- [ ] Delivery detail page loads correctly
- [ ] Delivery address shown prominently
- [ ] Cash payment shows red "COLLECT CASH" badge
- [ ] Zaad/Edahab shows green "ALREADY PAID" badge
- [ ] Status update shows correct next action button (e.g. "Mark as Picked Up")
- [ ] "Mark as Picked Up" confirmation dialog appears
- [ ] "Mark as On The Way" confirmation dialog appears
- [ ] "Mark as Delivered" shows cash collection confirmation note
- [ ] Delivered: order status updates to "delivered"
- [ ] Delivered: cash payment auto-confirmed (status = completed)
- [ ] Customer notification created on status update
- [ ] Admin notification created on status update

## 4. Profile and Notifications
- [ ] Notifications page loads correctly
- [ ] Mark all as read works correctly
- [ ] Profile edit with phone saves correctly
