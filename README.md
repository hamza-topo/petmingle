[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F5a6a25f1-3aee-4f4f-b463-74270242d0f3&style=plastic)](https://forge.laravel.com/servers/856396/sites/2516894)
# Pet Matching API Development Workspace

## 👋 Introduction

Welcome to the Pet Matching API development workspace. This space serves as a central hub for all documentation related to the API, providing a comprehensive guide for our team looking to integrate and understand the functionalities it offers.

## 🚀 Getting Started with this Workspace

### Reference Collections

Explore the following collections to gain insights into the capabilities of our Pet Matching API:

- [API Documentation Collection](#): This collection provides detailed requests and examples for creating, reading, updating, and deleting pet matches via the API.

Feel free to add `#reference` collections for your specific services to enhance the documentation.

### Blueprint Collections

Check out our self-service collections, such as:

- [RESTful API Basics Collection](#https://api.postman.com/collections/9516611-42d0d677-d200-4db3-8b02-b83293b9f08c?access_key=PMAT-01HQDG9RBG767P8A83WXAJ0G8C): This collection demonstrates workflows supported by our API using the fake-store API. You can directly send requests to observe and understand these workflows.

Consider adding `#blueprint` collections for essential workflows managed by your team.

## 🛟 Help and Support

If you have any questions, suggestions, or need assistance, please reach out to your manager or any member of the Engineering team.

## 🚧 API Details

- **Built with:** Laravel 8
- **Requires:** PHP 8
- **Note:** Additional features will be added as the API evolves.

## 🐳 Docker Development

This project includes a local Docker stack for Laravel 8:

- `nginx` serves the app on `http://localhost:8000`
- `app` runs PHP 8.2 FPM with Composer
- `mysql` runs MySQL 8
- `node` runs Laravel Mix asset watching when enabled
- `mailpit` captures local emails on `http://localhost:8025`

### First run

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec -u www-data app php artisan key:generate
docker compose exec -u www-data app php artisan migrate --seed
```

Open the application at `http://localhost:8000`.

### Useful commands

```bash
docker compose exec -u www-data app php artisan test
docker compose exec -u www-data app php artisan migrate:fresh --seed
docker compose exec -u www-data app composer install
docker compose run --rm node npm run dev
docker compose --profile assets up node
```

### Notes

- Composer dependencies live in a Docker named volume, so the host does not need PHP installed.
- Node dependencies live in a Docker named volume, so the host does not need Node installed.
- Database data is persisted in the `mysql_data` Docker volume.
- Local overrides can be placed in `docker-compose.override.yml`, which is intentionally ignored by git.

## 📱 Consumption Platforms

This API is designed to be consumed by a mobile app and a front-end framework (framework to be decided).

## 🌟 Contributing and Future Plans

We welcome contributions and feedback to enhance the Pet Matching API. As we move forward, we plan to expand its capabilities and integrations with additional platforms.

Thank you for being a part of our API development journey!
