# Robin Connect Example Implementation

## Installation

You can either install this project through `create-project robinhq/connect-server` inside the folder you want it to 
run from or by forking this repo to your own github repo and clone it from there. The last way is preferred, as you 
are able to test and develop locally and push to your own repo.
  
## Local Dev requirements

To be able to test and develop locally, you need to have [virtual box][Virtual Box] and [vagrant][Vagrant] installed on 
your machine. See Vagrant as a manager for virtual box.

Once you're done installing Virtual Box and Vagrant, head to the location you've installed this project in your 
terminal and run:
 
```bash
$ vagrant up
```

Initially, this will take some time. The next time you run the command, it'll be much faster as Vagrant has to download 
and configure the virtual machine on it's first run. When it's done, edit your `/hosts/etc` file (look up the 
location fo this file on Windows) and add the following line `192.168.10.10   robin-connect.app` save and close the 
file. Before we can view the application, we first have to set some environment variables.
 
 
## Setting .env variables
Robin Connect-Server requires a few api key's and other settings. You can see these settings when you open .env
.example. The first few lines are Lumen's environment settings. Below the line `Robin Connect-Server Settings` You 
can see the required variables this project needs in order to run. When you don't provide one of these, the project 
will be unable to run properly and you'll most likely encounter errors. To get you SEOShop API credentials, please 
contact SEOShop. The same is for your ROBIN API credentials, contact ROBIN to get them.

Once you have the API key's, you can copy the `.env.example` file and rename it to .env. Fill in all the variables 
values and last, but certainly not least add the url's to where you wan't SEOShop to send your hooks to by setting 
the `HOOK_BASE_URL` variable. This is the url where your application index is located. From here, are the hooks urls 
generated. So, when you install this application on the host `http://connect.mydomain.com` the SEOShop hooks that will
be registered are `http://connect.mydomain.com/hooks/orders` and `http://connect.mydomain.com/hooks/customers`.

## Installing Dependencies

In order to work properly, we need to install some dependencies. This is done through [composer]:[Composer] and 
[npn]:[Npn]. When you have both of the dependencies managers installed, you can do the following form inside the 
project root: 

```bash
composer install
cd public/js
npm install
```

After you have installed all of the dependencies, you can go to the development or production url and click on the 
`Register Webhooks` button to register the webhooks for SEOShop.

## Final Note

- Make sure you have set your webshop intergration to API in your ROBIN settings.
- SEOShop want's hook urls to have `http://` in front of them, even for a sub-domain.






[virtual box]: https://www.virtualbox.org/
[vagrant]: https://www.vagrantup.com/
[composer]: https://getcomposer.org/
[npm]: https://www.npmjs.com