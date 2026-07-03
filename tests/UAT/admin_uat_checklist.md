# Admin UAT Checklist

**Tester:** ___________
**Date:** ___________
**Browser:** ___________
**URL:** http://127.0.0.1:8000

## 1. Authentication
- [ ] Login as admin@loms.com redirects to `/admin/analytics`
- [ ] Unauthorized guest attempt on `/admin/analytics` redirects to `/login`
- [ ] Non-admin user attempt on `/admin/analytics` is blocked with 403 or redirected

## 2. Analytics Dashboard
- [ ] Page loads with current month data
- [ ] All period filters (today, last 7 days, last 30 days, this month, custom) work
- [ ] Custom date range works correctly
- [ ] All 9 charts render correctly
- [ ] KPI trend indicators show correctly (up/down arrows)
- [ ] Staff performance table shows correctly
- [ ] Delivery performance table shows correctly
- [ ] Recent orders table shows last 5 orders at the bottom of the page
- [ ] Recent support tickets table shows last 5 support tickets at the bottom of the page
- [ ] Export CSV downloads correctly
- [ ] Print page loads cleanly without sidebar

## 3. User Management
- [ ] View users list with pagination
- [ ] Search by name works correctly
- [ ] Filter by role works correctly
- [ ] Create new staff user successfully
- [ ] Edit user details successfully
- [ ] Toggle user active/inactive status
- [ ] Delete user with modal confirmation

## 4. Service Management
- [ ] View services list successfully
- [ ] Create service — slug auto-generated
- [ ] Edit service — slug updates
- [ ] Toggle service active/inactive status
- [ ] Delete service (no order items) — works
- [ ] Delete service (has order items) — blocked with error banner

## 5. Order Management
- [ ] View all orders with filters
- [ ] Search by order number works
- [ ] Filter by status works
- [ ] Filter by date range works
- [ ] View order detail page
- [ ] Update order status successfully
- [ ] Assign staff to order successfully

## 6. Delivery Assignment
- [ ] View delivery assignments list
- [ ] Create assignment for ready order
- [ ] Only orders with status "ready_for_delivery" in order dropdown
- [ ] Only delivery role users in agent dropdown

## 7. Support Messages
- [ ] Pending count badge visible in sidebar
- [ ] View all messages with filters
- [ ] Reply to pending message (reopens resolved status)
- [ ] Status changes to resolved after reply
- [ ] Mark message as ignored
- [ ] Reopen resolved/ignored message
- [ ] Export CSV downloads correctly

## 8. Reviews Management
- [ ] View all reviews list
- [ ] Filter by star rating works
- [ ] View review detail panel
- [ ] Delete review with modal confirmation
