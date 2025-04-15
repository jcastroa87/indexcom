# IndexCom

## Overview
**IndexCom** is an application designed to display the daily exchange index and visually represent progress through graphs. The project offers both a direct URL interface and API access, ensuring users can retrieve information in a straightforward and modern way.

## Project Details
- **Project Name:** IndexCom
- **Description:** An application to show daily exchange index and the graph with progress in a beautiful and simple way, providing direct access by URL or via API.
- **Primary Goal:** Deliver a clean, accessible, and robust interface for tracking exchange indexes using modern design principles and best practice development techniques.

## Technologies Used
- **Laravel:**  
  - A powerful PHP framework that provides a robust foundation for modern web applications.
  - **Best Practices:**  
    - Follow PSR-12 coding standards and Laravel’s conventions.
    - Maintain clear separation between models, views, and controllers (MVC).
    - Utilize Eloquent ORM for database interactions.
    - Secure the application with proper middleware, input validation, and CSRF protection.
    - Manage environment-specific configurations using the `.env` file.

- **Backpack for Laravel:**  
  - A ready-made admin interface package that significantly speeds up the development of CRUD operations.
  - **Best Practices:**  
    - Leverage Backpack’s built-in operations for common tasks.
    - Customize fields, validation, and views as per the official documentation.
    - Keep custom modifications modular and consistent with Backpack’s extension patterns.
    - Maintain up-to-date documentation for any custom changes to make onboarding easier.

- **Tabler Admin Template:**  
  - A modern and responsive admin template that enhances the user interface with a clean design.
  - **Best Practices:**  
    - Integrate the template with Laravel’s Blade templating for seamless UI development.
    - Use Laravel Mix (or your preferred asset bundler) to compile and optimize Tabler’s assets.
    - Ensure responsiveness and accessibility by adhering to Tabler’s design guidelines.
    - Customize the theme as necessary while keeping updates manageable with a clear asset management workflow.

- **MCP Modules:**  
  - **Memory MCP:**  
    - A module from the [Model Context Protocol](https://github.com/modelcontextprotocol/servers/tree/main/src/memory) used for managing ephemeral data, caching, and context management.
    - **Best Practices:**  
      - Install and configure Memory MCP following the repository guidelines.
      - Integrate Memory MCP as a dedicated service within your Laravel application using dependency injection.
      - Use it for managing session data, caching temporary states, or storing context information across requests.
      - Document any custom configurations or extensions for team clarity.
  - **Brave-Search:**  
    - A search module provided by MCP from [this repository](https://github.com/docker/mcp-servers/tree/main/src/brave-search), intended to enhance search functionality.
    - **Best Practices:**  
      - Follow the repository instructions to properly install and set up Brave-Search.
      - Integrate it as a service within your application to power internal search or external query capabilities.
      - Ensure the module is configured for optimal performance and security.
  - **Fetch Module:**  
    - A data retrieval module available at [this repository](https://github.com/modelcontextprotocol/servers/tree/main/src/fetch) designed to handle external API requests.
    - **Best Practices:**  
      - Install and configure the Fetch module following the provided documentation.
      - Use the module to robustly fetch daily exchange rates or related data, especially as a fallback when other APIs fail.
      - Monitor and handle errors gracefully, and document any custom settings for consistency.

## Best Practices

Below is a comprehensive guide outlining best practices when coding in Laravel. These practices help you write clean, maintainable, and efficient code while taking full advantage of Laravel’s powerful features. You can incorporate these guidelines into your workflow whether you’re starting a new project or refining an existing one.

## Development Guidelines

1. **Follow the Laravel Conventions and MVC Architecture**
    - **Embrace the MVC Pattern:**  
        - Separate concerns by using models for data, controllers for business logic, and views for presentation.
        - Keep controllers lean; delegate complex logic to dedicated service classes or repositories.
    - **Directory Structure:**  
        - Use Laravel’s default directory structure to maintain consistency.
        - Consider additional folders like `Services`, `Repositories`, or `Jobs` for extra logic.

2. **Adopt Consistent Coding Standards**
    - **PSR-12 Compliance:**  
        - Follow the PSR-12 coding standard for PHP.
        - Employ tools such as PHP CS Fixer or PHP_CodeSniffer to automate enforcement.
    - **Naming Conventions:**  
        - Use clear and descriptive names for classes, methods, and variables (camelCase for variables/methods, PascalCase for classes).
    - **Commenting and Documentation:**  
        - Write meaningful inline comments and use PHPDoc for thorough documentation.

3. **Manage Environment Configuration Effectively**
    - **Use the `.env` File:**  
        - Store configuration settings, API keys, and secrets in the `.env` file.
        - Do not commit this file; use `.env.example` as a template.
    - **Configuration Caching:**  
        - Cache configuration using `php artisan config:cache` and refresh upon changes.

4. **Leverage Eloquent ORM Wisely**
    - **Relationship Management:**  
        - Utilize Eloquent relationships to simplify data interactions and use eager loading (with()) to optimize performance.
    - **Query Building:**  
        - Use the query builder for complex queries, ensuring input validation and sanitization.
    - **Mass Assignment:**  
        - Protect models using `$fillable` or `$guarded` properties.

5. **Emphasize Security Best Practices**
    - **Input Validation and Sanitization:**  
        - Use Laravel’s request validation and CSRF protection.
    - **Authentication and Authorization:**  
        - Implement Laravel’s authentication scaffolding or packages like Laravel Fortify; use policies/gates for permissions.
    - **Error Handling:**  
        - Centralize error handling via Laravel’s exception handler and provide user-friendly error messages.

6. **Write Tests to Ensure Code Reliability**
    - **Automated Testing:**  
        - Use PHPUnit and Laravel’s testing utilities for unit and feature tests to ensure high coverage.
    - **Test-Driven Development (TDD):**  
        - Consider TDD to guide design and maintain overall code quality.

7. **Optimize Application Performance**
    - **Caching Strategies:**  
        - Utilize Laravel’s caching mechanisms (file, Redis, Memcached) to store heavy computations or queries.
    - **Queueing and Background Jobs:**  
        - Offload heavy tasks using Laravel Queues and monitor for failures.
    - **Optimized Query Performance:**  
        - Profile and optimize queries with proper indexing and batching where applicable.

8. **Utilize Dependency Injection and Service Providers**
    - **Dependency Injection:**  
        - Favor constructor injection for dependencies to improve testability and decouple components.
    - **Service Providers:**  
        - Register custom services, event listeners, or bindings within service providers, keeping them lean.

9. **Organize Routes and API Endpoints**
    - **Route Organization:**  
        - Use Laravel’s resourceful routing for web and API endpoints; group routes by feature and cache with `php artisan route:cache`.
    - **RESTful API Design:**  
        - Follow REST conventions with proper HTTP verbs, resource-based endpoints, clear status codes, and versioning.

10. **Embrace Laravel’s Ecosystem and Tools**
    - **Artisan CLI:**  
        - Leverage Artisan for tasks such as migrations, testing, and creating boilerplate code.
    - **Laravel Mix:**  
        - Manage and compile front-end assets efficiently with Laravel Mix.
    - **Regular Dependency Updates:**  
        - Keep Laravel and packages up-to-date, reviewing release notes and update guidelines regularly.

11. **Integrate and Manage MCP Modules**
    - **Memory MCP:**  
        - Install and configure Memory MCP using guidelines from its [repository](https://github.com/modelcontextprotocol/servers/tree/main/src/memory).
        - Integrate it as a dedicated service for handling ephemeral data, caching, or session management.
    - **Brave-Search Module:**  
        - Follow instructions from [Brave-Search repository](https://github.com/docker/mcp-servers/tree/main/src/brave-search).
        - Integrate it to power internal or external search functionalities, ensuring secure and optimized configurations.
    - **Fetch Module:**  
        - Configure the Fetch module per the [Fetch repository](https://github.com/modelcontextprotocol/servers/tree/main/src/fetch).
        - Use it to robustly fetch external API data (e.g., daily exchange rates), with proper error handling and fallback mechanisms.
    - **General Guidelines for MCP Modules:**  
        - Use dependency injection to manage each module’s integration.
        - Monitor performance and handle exceptions to ensure stability.
        - Document any custom configurations or extensions for ease of maintenance.

- **Code Structure & Standards**  
    - **Laravel:** Adhere to the conventional MVC structure; keep business logic in controllers and models, and maintain focused views.
    - **Backpack:** Use Backpack’s CRUD controllers and operations, extending only when necessary per documentation.
    - **General:** Follow PSR-12 coding standards and ensure code is well-commented and modular.

- **API & Routing**  
    - **Routing:** Use Laravel’s resourceful routing for both web and API endpoints; secure API routes in `routes/api.php` with middleware.
    - **API Design:** Adopt RESTful API principles, document endpoints and version them, and utilize Laravel API resources for JSON responses.

- **Testing & Quality Assurance**  
    - **Testing:** Write comprehensive tests using Laravel’s PHPUnit integration—focus on both unit tests and feature tests.
    - **Code Reviews:** Conduct peer reviews and employ continuous integration to automatically run tests on new commits.

- **Deployment & Maintenance**  
    - Keep dependencies updated; document and monitor changes to routing, database schema, or UI components.
    - Employ appropriate logging and error handling to monitor production performance.

- **Contribution Guidelines**  
    - **Commit Messages:** Write clear and concise messages following conventional commit formats.
    - **Pull Requests:** Ensure PRs pass CI checks, include detailed explanations, and reference relevant issues or discussions.
    - **Documentation:** Update this file and inline code comments with any significant changes.

- **Final Notes**  
    - **Security:** Validate inputs, gracefully handle exceptions, and protect against common vulnerabilities.
    - **Collaboration:** Use GitHub issues and discussions to refine new features.
    - **Consistency:** Maintain consistency across all modules (Laravel, Backpack, Tabler, and MCP Modules) to ensure a cohesive user experience and maintainable codebase.
