# Easter App

This application in a project cost evaluation application.
It helps you to calculate a project cost based on the project feature and the project team experience.

You can edit up to 3 project variants to evaluate the best cost/performance solution.

Once this best solution is choosen, you can print it as a pdf document.

## Getting Started

### Prerequisites

1. Check composer is installed
2. Check yarn & node are installed

### Install

1. Clone this project
2. Run `composer install`
3. Run `yarn install`

4. Set the database url into the env.local file  
```DATABASE_URL=mysql://databaseUserName:databaseUserPassword@databaseServerIpAdress:databaseServerPort/databaseName```

5. Set the e-mail dsn into the env.local file (so the application can send e-mail)  
```MAILER_DSN=smtp://*emailAdress*:*mailboxUserPassword*@default```  
```MAILER_FROM_ADDRESS=*emailAdress*```  

6. Make the database, tables and create an administrator account  
```bin/console doctrine:database:create``` 
```bin/console doctrine:schema:update --force```  
```php bin/console doctrine:fixtures:load --group=UserFixtures --append```  

7. Change your admin login and password
The initial administrator account is `johndoe@easterapp.fr` with password `adminpassword`. Make sure to change them to prevent anyone to access your data

8. If needed, you can fill the database with fake records for testing purpose.  bin/console doctrine:fixture:load

#### Installation troubleshooting

##### missing extension 

Depending on your php installation, the `composer install` command can trigger an error message saying that some extension are missing.

If you are using php 7.2 on Ubuntu and the message say that mbstring is missing run the following command :  
`sudo apt-get install php7.2-mbstring`

Next continue from the step 2 of the install paragraph.

##### php version

This application is made on php 7.2. It may happen that `composer install` release an error message saying that composer.lock is made for php 7.3 and cannot be installed on 7.2 php version.
To install the application on php 7.2, run the following command  
`composer dump_autoload`

Next continue from the step 2 of the install paragraph.

### Working

1. Run `symfony server:start` to launch your local php web server
2. Run `yarn run dev --watch` to launch your local server for assets

### Testing

1. Run `./bin/phpcs` to launch PHP code sniffer
2. Run `./bin/phpstan analyse src --level max` to launch PHPStan
3. Run `./bin/phpmd src text phpmd.xml` to launch PHP Mess Detector
3. Run `./bin/eslint assets/js` to launch ESLint JS linter
3. Run `./bin/sass-lint -c sass-linter.yml` to launch Sass-lint SASS/CSS linter

## Built With

* [Symfony 4.4](https://github.com/symfony/symfony)
* [GrumPHP](https://github.com/phpro/grumphp)
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
* [PHPStan](https://github.com/phpstan/phpstan)
* [PHPMD](http://phpmd.org)
* [ESLint](https://eslint.org/)
* [Sass-Lint](https://github.com/sasstools/sass-lint)
* [Travis CI](https://github.com/marketplace/travis-ci)
* [Webpack Encore](https://symfony.com/doc/current/frontend.html#webpack-encore)
* [Dompdf](http://dompdf.github.com/)
* [Mjml](http://mjml.io)
* [Easyautocomplete](http://easyautocomplete.com/)
* [Paginator](https://github.com/KnpLabs/KnpPaginatorBundle)
* [Vich uploader 1.11](https://github.com/dustin10/VichUploaderBundle)


## Versioning


## Authors

Wild Code School trainers team

Adrien MAILLARD  
Benoît CHOCOT  
Anthony ROSSIGNOL  
Dewi DIERICK  

## License

MIT License

Copyright (c) 2019 aurelien@wildcodeschool.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

## Acknowledgments

