## First time
1. run ```composer install```
2. run ```sh scripts/generate_pdf.sh```
this will start the MYSQL server prepare the DB, clone the OWASP SAMM repo and use it to populate the DB, then the pdf will be generated under /export folder. 

## Keep-alive
1. run ```sh scripts/generate_pdf.sh -a``` option -a will keep the mysql server running, but then you have to stop it manually
2. generate the pdf
3. run ```sh scripts/docker-mysql-stop.sh``` to stop the mysql image 

## Generating the PDF
If you want to regenerate the PDF after the setup is done and the mysql server is running, you can run ```php bin/console app:save-pdf```
