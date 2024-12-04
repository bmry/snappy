# My Application Setup Instructions

## Table of Contents

- [Setup and Installation](#setup-and-installation)
- [Job Processing](#job-processing)
- [Postcode Import](#postcode-import)
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

```
### API Documentation
[Click here to open API documentation](http://localhost/api/documentation)
### Job Processing
```bash
# Open a new terminal and start the queue
./vendor/bin/sail exec snappy-app  php artisan queue:work
```
### Postcode Import
```bash
# In a new terminal, run the postcode command
./vendor/bin/sail exec snappy-app  php artisan postcodes:import
```

### Testing
```bash
./vendor/bin/sail exec snappy-app  php artisan test
```
