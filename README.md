# Prismix Status Monitor (Symfony & Docker)

A web application built with **Symfony 7.4** and **PHP 8.3** for monitoring and presenting real-time service statuses fetched from the external [Prismix API](https://prismix.dev/api/v1/statuses).

---

## 🚀 About the Application

The application connects to the public Prismix API endpoint, retrieves live infrastructure metrics, and presents them in an interactive, responsive table.

### Key Features:
- **Metrics Fetching & Mapping:**
  - **Service Name** (`name`) – Identifier of the monitored service.
  - **Service Description** (`description`) – Detailed component status.
  - **Response Latency** (`latencyMs`) – Real-time latency in milliseconds with visual indicators.
  - **30-Day Availability / Uptime** (`uptime30dPct`) – Percentage SLA uptime indicator with automated color badges (green $\ge$ 99%, yellow $\ge$ 95%, red < 95%).
- **Dynamic Client-Side Filtering (TableFilter):** Real-time column filtering, multi-field search, visible row counters, and a single-click filter reset button.
- **Responsive UI:** Built with **Bootstrap 5.3** and **Bootstrap Icons**, optimized for desktop and mobile viewports.
- **Resilience & Type Safety:** Strict DTO mapping (`ServiceStatus`), robust error handling, and logging for external API failures without disrupting application runtime.
- **Automated Test Coverage:** Full suite of unit and integration tests using **PHPUnit**.

---

## 🛠️ Prerequisites

To run this application in Docker containers, ensure you have installed:
- **Docker Engine** (version 20.10+ or later)
- **Docker Compose** (version v2+ / `docker compose`)

---

## 🐳 Docker Architecture

The Docker environment (`docker-compose.yml` + `Dockerfile`) consists of the following services:
- **`app` (`symfony_app`):** PHP 8.3 FPM runtime with required extensions (`pdo_mysql`, `intl`, `opcache`, `zip`, `apcu`), Composer 2, Node.js 20, and Symfony CLI.
- **`nginx` (`symfony_nginx`):** Nginx Alpine web server mapping host port `8080` to container port `80`, routing PHP requests to the `app` container (port 9000).
- **`db` (`symfony_db`):** MySQL 8.0 database service (available on port `3306`).

---

## 📖 Getting Started Tutorial (Step-by-Step)

### Step 1: Clone the Repository
Clone the repository to your local machine and navigate into the project directory:
```bash
git clone <repository-url>
cd symfony
```

### Step 2: Build and Start Docker Containers
Build the container images and launch the services in detached mode:
```bash
docker compose up -d --build
```
> This starts `symfony_app`, `symfony_nginx`, and `symfony_db` containers.

### Step 3: Install Dependencies (Composer & npm)
Install PHP packages and frontend assets inside the application container:
```bash
docker compose exec app composer install
docker compose exec app npm install
```

*(Optional)* Clear and warm up the Symfony cache if needed:
```bash
docker compose exec app bin/console cache:clear
```

### Step 4: Open the Application in Your Browser
The application is accessible directly at:

👉 **[http://127.0.0.1:8080](http://127.0.0.1:8080)** or **[http://127.0.0.1:8080/statuses](http://127.0.0.1:8080/statuses)**

---

## 🧪 Running Automated Tests

Run the full PHPUnit test suite (controllers, API integration service, DTOs) inside the container with:

```bash
docker compose exec app bin/phpunit
```

---

## 📋 Useful CLI & Development Commands

- **Check container status:**
  ```bash
  docker compose ps
  ```

- **View container logs in real time:**
  ```bash
  docker compose logs -f
  # or for a specific service:
  docker compose logs -f app
  docker compose logs -f nginx
  ```

- **Open a bash shell inside the PHP app container:**
  ```bash
  docker compose exec app bash
  ```

- **Run Symfony console commands:**
  ```bash
  docker compose exec app bin/console <command-name>
  ```

- **Stop Docker containers:**
  ```bash
  docker compose down
  ```
  *(To also remove data volumes, append `-v`: `docker compose down -v`)*

---

## 📁 Project Directory Structure

```text
├── assets/                 # Frontend assets (JavaScript, CSS, Stimulus controllers)
├── bin/                    # Executable binaries (console, phpunit)
├── config/                 # Symfony configuration and bundle packages
├── docker/                 # Docker configuration files (Nginx host config, PHP ini)
│   ├── nginx/default.conf  # Nginx virtual host configuration
│   └── php/php.ini         # Custom PHP FPM ini settings
├── public/                 # Web server root (index.php) and static public assets
├── src/                    # Application source code
│   ├── Controller/         # Web controllers (StatusController)
│   ├── Dto/                # Data Transfer Objects (ServiceStatus)
│   ├── Kernel.php          # Symfony Kernel definition
│   └── Service/            # Business services and API clients (PrismixStatusService)
├── templates/              # Twig templates (base.html.twig, status/index.html.twig)
├── tests/                  # PHPUnit automated tests (Controller, Dto, Service)
├── Dockerfile              # PHP 8.3 FPM image specification
├── docker-compose.yml      # Docker Compose multi-container services definition
└── phpunit.dist.xml        # PHPUnit test runner configuration
```
