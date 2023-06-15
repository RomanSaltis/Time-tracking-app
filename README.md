# Time-tracking-app
Simple Time tracking application, where user can register with their email and password. After successful registration, the user is automatically logged in, where they can create task entries with time it took them to complete it. Each user should have unique email address, and can only see entries that they created.

## Getting Started

### Prerequisites

- PHP (version 8.1.10)
- Laravel (version 10.13.0)
- PostgreSQL (version 14.5)

### Installation

1. Clone the repository:

   https://github.com/RomanSaltis/Time-tracking-app

2. Install dependencies:
   composer install

3. Configure the environment variables:

    Update the .env file
    APP_NAME=Laravel
    APP_ENV=local
    APP_KEY=base64:recO45iYB/V0xV/I0f5hvPv/uBGnI3FiOl9rPdAQYso=
    APP_DEBUG=true
    APP_URL=http://localhost

4. Run database migrations:

    php artisan migrate

### Usage

1. Start the local development server:

   php artisan serve

2. Open your web browser http://localhost:8000 to access the application.

3. Register a new user account with your email and password.

4. After successful registration, you will be automatically logged in.

5. Create new task 

6. View and manage your task entries from the application's interface.

7. Generate and download report file (in pdf, csv or excel format) by date range

8. To run the test cases, run the following command:
   php artisan test

### License Information:

This project is licensed under the MIT License (opensource.org/licenses/MIT).

### Contact

For any inquiries or questions, please contact roman.saltis@gmail.com


## Functionality

The Time-tracking-app provides the following key features:

### User Registration and Authentication

1. Users can register with their email and password.

2. After successful registration, users are automatically logged in.

3. Each user must have a unique email address.

4. The application supports two global users:
   - is_superadmin($user): This function checks if a user is the super admin based on their specific user ID 
defined in the configuration file config('app.admin_user_id').
   - global user(): This function returns the currently logged-in user.

### User Profile Management

1. Users can view and manage their own profile information, including name, email, and other details.

2. Super admin users (is_superadmin($user)) have the ability to view and manage all user profiles, 
including updating their information. 

### Task Management

1. Users can create task entries and specify the time it took them to complete each task.

2. Task entries include details such as title, comment, date, and time spent.

3. Users can view and manage their own task entries.

4. Super admin users (is_superadmin($user)) have the ability to view and manage all user tasks,
   including updating their information.

### Reporting

1. Users can generate and download their task reports based on a date range.

2. Reports can be generated in PDF, CSV, or Excel format.

3. Reports provide a summary of the user's task entries within the specified date range.

4. Super admin users (is_superadmin($user)) have the ability to generate and download all users
task reports based on a date range.

### Testing

1. The application includes unit and functional tests to ensure the functionality is working correctly.

2. You can run the test cases running command
   php artisan test 
