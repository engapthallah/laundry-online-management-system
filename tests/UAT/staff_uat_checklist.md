# Staff UAT Checklist

**Tester:** ___________
**Date:** ___________
**Browser:** ___________
**URL:** http://127.0.0.1:8000

## 1. Authentication
- [ ] Login as staff@loms.com redirects to `/staff/dashboard`
- [ ] Cannot access `/admin`, `/customer`, `/delivery` paths (blocked or redirected)

## 2. Staff Dashboard
- [ ] 4 stat cards visible and correct (Assigned, Processing, Ready, Completed)
- [ ] Active order cards shown in task board
- [ ] Urgency borders shown for near-deadline orders (e.g. yellow or red accents)
- [ ] Charts render correctly

## 3. Order Processing
- [ ] My Orders list shows only assigned orders
- [ ] Search and filter by order number/date work
- [ ] Order detail page loads correctly
- [ ] Special instructions highlighted clearly
- [ ] Status update panel shows correct next status action button (e.g., "Start Washing")
- [ ] Confirmation dialog appears before status update
- [ ] Status updates sequentially (e.g., confirmed → washing → drying)
- [ ] Cannot skip statuses (validated on POST/PATCH)
- [ ] Customer notification created after status update
- [ ] Success message shown after update

## 4. Profile and Notifications
- [ ] Notifications page loads correctly
- [ ] Unread notifications shown with blue/highlighted background
- [ ] Mark all as read works correctly
- [ ] Profile edit details save successfully
