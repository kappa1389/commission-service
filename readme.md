## Summary
This application is a service to calculate commissions for already made transactions

## Installation guide

### Clone the project
Clone this repository to your local machine using the following command:
```bash
git clone git@github.com:kappa1389/commission-service.git
```

### Running containers
Open `Terminal` and type the following command:
```bash
docker-compose up -d
```

#### Installing the dependencies
Make an ssh connection to the `core` container using this command:  

```
   docker exec -it commission-core bash
```  

Now simply install the dependencies via composer
```bash
composer install
```

### Run Tests
Run tests using this command inside core container
```bash
vendor/bin/phpunit
```

### Manual test
Run this command inside core container to manually test the application, you need internet access for this,
you can modify public/sample.txt file and add transactions for manual test
```bash
php public/app.php public/sample.txt
```

## Technical discussions (Images/Containers)
This project includes one docker container as follows.

`core`
php:8.0-fpm
