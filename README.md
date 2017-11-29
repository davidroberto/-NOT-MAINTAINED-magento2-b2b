# magento2-b2b

Restrict access to a website for a group of customers

## Getting Started

If you don't already have a multi-websites Magento, you first you need to create a new website (admin->stores). 
Then install the module from Composer (the module is listed in Packagist), or clone it from Github. 
You'll have a new "B2B" tab in the configuration page, where you'll be able to select a website and a group of customers (a "wholesale" groupe for example). 

Save it, and it's done! The website selected is now accessible only by the customers from the selected group of customers.
If the user is not identified and try to access the website, he'll be redirected to the login form. If, after loggin, it doesn't belong
to the corrected group, he'll be redirected to the default website.
