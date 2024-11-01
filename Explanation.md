# Cool Kids Network Plugin - Explanation

## Problem Statement

The Cool Kids Network plugin is designed to bring a unique role-based experience to an online community, where each user's role governs their data visibility within the network. This proof-of-concept plugin allows users to join the network, view their profiles, and access other users’ information based on specific roles. Additionally, admins have tools to manage data access, set up custom blocks for signup and login, and more.

## Technical Specification

The plugin’s key functionalities include:

1. **User Roles**: On activation, the plugin creates user roles: "Cool Kid," "Cooler Kid," and "Coolest Kid." Each role has defined permissions, with the “Cool Kid” role as the default for new signups. These roles determine data visibility levels for different users.

2. **Automatic Page Creation on Activation**: The plugin automatically creates four essential pages on activation:
   - Login Page (`login-page`)
   - Profile Page (`profile-page`)
   - Signup Page (`signup-page`)
   - User Directory Page (`user-directory`)

   Each page has the corresponding custom block added, so it is set up for immediate use without any further admin configuration.

3. **Custom Blocks**:
   - **Signup Block**: Provides a signup form with an email field. When submitted, a unique profile is generated using randomuser.me API, which populates fields like name and country.
   - **Login Block**: Allows users to log in with their email only, simplifying access.
   - **Profile Block**: Displays the logged-in user’s profile information (first name, last name, country, email, role) with visible fields controlled by admin-configured settings.
   - **User Directory Block**: Lists all users in a table format, displaying fields such as name, country, email, and role, based on role-based visibility settings.

4. **Admin Settings Pages**:
   - **Settings Page**: Admins can bulk-generate demo users using the randomuser.me API, helping to quickly populate the network for testing. Admins also see a success notice when demo users are created.
   - **User Role Fields**: Allows admins to select which fields (first name, last name, country, email, role) are visible to each role in the user directory.

5. **Frontend Access Control**: The user directory is filtered based on visibility settings configured in “User Role Fields,” ensuring users see only permitted information. Additional filters for "Country" and "User Role" allow users to find specific profiles within the user directory.

## Comparison of Requirements and Final Implementation

Here’s how each requirement in the user stories was met and enhanced:

### User Story 1: Sign-up and Character Generation

- **Requirement**: A signup page with an email field and “Confirm” button. A valid, unused email creates an account, generating a character profile with randomuser.me API data.

- **Enhancement**: The plugin adds an admin notice recommending the “Cool Kids Network Theme” to enhance user experience, encouraging a cohesive look for the network.

### User Story 2: Login and Character Data View

- **Requirement**: A login page where users enter their email to access their profiles without a password. Users can view their profile information, including first name, last name, country, email, and role.

- **Enhancement**: The login process redirects logged-in users to their profile instead of signup or login pages, improving navigation. Admins can also customize profile visibility fields for each role through settings.

### User Story 3: Name and Country Access for Cooler/Coolest Kid

- **Requirement**: Cooler Kid and Coolest Kid roles should be able to view names and countries of all users. Cool Kid role should not access this feature.

- **Enhancement**: The “User Role Fields” settings provide detailed visibility control, allowing admins to specify which fields are viewable by each role. Additionally, admins and maintainers have full access to all fields.

### User Story 4: Email and Role Access for Coolest Kid

- **Requirement**: Coolest Kid role should view both the email and role of all users.

- **Enhancement**: Admins can configure which fields the Coolest Kid role can view, expanding beyond email and role to first name, last name, and country as needed.

## Extra Features and Improvements

The plugin includes these additional features:

1. **Automatic Page Creation and Block Assignment**: On activation, the plugin creates all necessary pages and automatically assigns the appropriate block to each page, making the setup process seamless for admins.

2. **Demo Users Generator**: Admins can generate demo users on the Settings page, creating test profiles using randomuser.me API. Upon generation, a success message confirms the action, enhancing the admin experience.

3. **Role-Based Access Control (RBAC) Settings**: Provides checkboxes for each role and field, allowing admins to control which data each role can view in the user directory.

4. **Country and Role Filters in User Directory**: The User Directory now includes filters for "Country" and "User Role," enabling users to quickly find and organize specific profiles.

5. **Admin Notices and Theme Recommendation**: On activation, an admin notice recommends the “Cool Kids Network Theme” for enhanced interaction.

6. **Modular Design and Autoloading**: Each feature is separated into classes under `includes/` for maintainability, and an autoloader ensures efficient loading.

## Technical Decisions

### Why Enhance Beyond Requirements?

The original requirements offered a solid foundation but left room for flexibility, control, and customization, which are essential for scalable and user-friendly WordPress plugins. By extending features and adding more granular control for admins, I ensured the plugin could adapt to broader use cases and provide a more refined experience. Here’s why each enhancement was considered:

- **Role-Based Access Control (RBAC)**: Adding field-specific visibility control for each user role allows greater flexibility. This addresses potential needs in larger communities where user data privacy and varied access levels are important.
- **Demo User Generation**: This feature assists with easy testing and onboarding, especially for admins managing user-heavy networks.
- **Theme Recommendation Notice**: Integrating a theme enhances user experience, providing consistency in UI/UX and encouraging users to get the most from the plugin’s interactive features.
- **Automation and Seamless Setup**: Automatically setting up roles, pages, and blocks improves ease of use, allowing the plugin to be deployed on any new WordPress installation with minimal setup.

This approach aligns with WordPress’s emphasis on extendable, adaptable plugins and delivers a more comprehensive solution to the user stories.

## Special Note

This plugin is designed to work out of the box without requiring additional database configurations. All necessary setup, including page and role creation, occurs automatically on activation. You can install this plugin on a fresh WordPress instance, and it will be ready to use immediately.

While I wasn’t able to include some of the bonus points due to focusing on core features and enhancements, I aimed to create a highly functional, user-centered plugin. I also envisioned a special theme to complement this plugin but didn’t have the time to develop it fully. I hope you’ll appreciate what I was able to achieve with this plugin!

## Summary

The Cool Kids Network plugin fulfills all user story requirements with added advanced RBAC controls and admin tools. It offers an extended user directory experience, a streamlined login/signup process, and flexible configuration options through its settings pages, creating a feature-rich network experience.
