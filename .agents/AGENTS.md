# HK Garage System - Agent & Copilot Rules

## Email Notifications Requirement
Use PHPMailer with Aruba SMTP (`smtps.aruba.it`, Port 465 / 587) to send professional HTML emails after every successful booking.

### Requirements:
1. **Customer Confirmation**: Send booking confirmation to the customer's email.
2. **Garage Notification**: Send notification email to the garage (`appointments@hkgarage.it`) for every new booking.
3. **Aruba SMTP Authentication**: Use SMTP authentication with Aruba Mail.
4. **Sender Identity**: Email sender MUST be `HK Garage <appointments@hkgarage.it>`.
5. **Responsive HTML Template**: Design responsive HTML emails including:
   - HK Garage logo & branding header
   - Booking ID
   - Selected Service Name
   - Appointment Date & Time
   - Vehicle Details (Brand, Model, Registration plate)
   - Workshop Address (Via Consortile della Conta, 3 - 24060 Costa di Mezzate BG)
   - Contact Info (+39 035 123 4567, appointments@hkgarage.it)
6. **Graceful Fail-Safe**: If email sending fails, the booking MUST still be saved in the database, and the error should be logged via `error_log()` without affecting the user experience.
7. **Environment Variables**: Keep SMTP credentials in environment variables or `.env` / configuration file (`includes/config.php`), NEVER hard-code secrets.
