## First time
1. Run ```composer install```
2. run ```sh scripts/generate_pdf.sh```
step 2 will prepare the DB, clone the OWASP SAMM repo and use it to populate the DB, then the pdf will be generated under /export folder. 

## Generating the PDF
If want to regenerate the PDF after the setup is done run ```php bin/console app:save-pdf```

## Syncing from yaml files
After the project setup in order to fill the empty database with records do the following:

 ### Automatic:
 Run the following script (sudo is NOT needed):
 ```
 sh scripts/clone_owasp_samm.sh 
 ```

### Manual (if you are a pro or have nothing better to do):
Make 'private' folder in the project. Then do
 ```
 git clone https://github.com/owaspsamm/core.git
 ```

After that and from then on you can use the following command when you need to sync from the cloned YAML files.
 ```
 php bin/console app:sync-from-owasp-samm
 ```
