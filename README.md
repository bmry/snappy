# My Application Setup Instructions

## Table of Contents

- [Setup and Installation](#setup-and-installation)
- [Postcode Import](#postcode-import)
- [Job Processing](#job-processing)
- [Caching](#caching)
- [API Documentation](#api-documentation)
- [Testing](#testing)

### Setup and Installation

Follow these steps to set up the application:

```bash
# Clone the repository
git clone git@github.com:bmry/snappy.git
cd snappy

# Start the Docker containers using Sail
./vendor/bin/sail up -d

# Install dependencies
./vendor/bin/sail exec snappy-app composer install    

# Run migration
./vendor/bin/sail exec snappy-app php artisan migrate    

# Open a new terminal and start the queue
./vendor/bin/sail exec snappy-app  php artisan queue:work
```

### Postcode Import
```bash
# In a new terminal, run the postcode command
./vendor/bin/sail artisan postcodes:import
```

### Testing
```bash
./vendor/bin/sail artisan test
```
