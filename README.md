# magento2-delete-category-products-bySkus

# Description 
magento2 Enterprise edition delete category products bySkus If your merchant uses Visual Merchandiser Module on the tab Products In Category in the admin categories and you want to implement a bulk delete then this is your module.
*	The module will allow you to download/export the Skus for each category products.
*	After downloading you will insert skus in the box at the bottom of the module which will be created by the xml ui_comp file.
*	For this to work smooth make sure that there is no space between the skus and skus are separated by a comma (,) like 123121,1231231,134141,1341341,134143


# Setup the module on your magento 

* Download the files 
* Create your file path on your app/code/F8/Catalog
* copy the file from downloads and paste them there and run commands

``` 
php bin/magento module:enable F8_Catalog &&
php bin/magento setup:upgrade && 
php bin/magento cache:flush && php bin/magento cache:clean  && 
php bin/magento setup:di:compile && 
php bin/magento setup:static-content:deploy -f
```

# Good luck: for any questions you can contact maneza on 

* Website: https://manezaf8.co.za/fku-podcast

* Twitter: https://twitter.com/manezaf8/

* Facebook: https://facebook.com/fkuafrica


