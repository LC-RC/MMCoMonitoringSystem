# Database Setup

Run these **in order** (e.g. in phpMyAdmin or MySQL CLI):

| Order | File | Purpose |
|-------|------|---------|
| 1 | `schema.sql` | Creates database and base tables (users, attendance, projects, etc.) |
| 2 | `migration_register_wizard.sql` | Adds registration columns (first_name, personal_email, department VARCHAR, etc.) |
| 3 | `migration_birthday_address.sql` | Adds birthday, address, gender, civil_status |
| 4 | `migration_employee_id.sql` | Adds employee_id (for auto-generated IDs) |
| 5 | `insert_admin_now.sql` | Creates/resets admin (admin.mmco@gmail.com / password123) |

After step 5 you can log in as admin. New users register via the app; their data is stored in the `users` table.
