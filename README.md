## Taxonomies

Bringing Taxonomies to MODX Revolution: add Categories or Tags to any MODX page.


Taxonomies are classifications like "Categories" or "Tags" that help you to organize and catalogue
pages on your site so they are easier to find.  The difference between taxonomical terms and custom
fields can get become blurry, but the distinction is that taxonomies are intended solely to improve
searching on your site, and the relationship between a page and its terms is always one-to-many.

Unlike other solutions, this package uses custom tables to store the relational data so that the results
are easily (and quickly) queried.



## Installation

Install this package via the MODX package manager.



### Advanced Installation (Developers Only)


This package was built using Repoman (https://github.com/craftsmancoding/repoman) to make it easier
for others to contribute to the code.

To install the package using Repoman:

1. Install Repoman
2. Clone this project into a directory inside your MODX webroot:

    git clone https://github.com/craftsmancoding/taxonomies

3. Run composer install to generate the autoload files:

    cd path/to/taxonomies/
    composer install
        
4. Run the Repoman "install" utility on the newly created taxonomies directory:

    php path/to/repoman/repoman install path/to/taxonomies/

This will create the necessary objects and custom tables.


## Usage

Once the Taxonomy package has been installed, you can create new taxonomies by right-clicking on the MODX
page tree and select "Create --> Create a Taxonomy Here".  

Taxonomy: a container for terms.  Common taxonomies are "Categories" and "Tags".  E.g. "Genres".
Terms: an item inside the given taxonomy, e.g. "Sci-Fi" or "Romance"

The Taxonomy and Term pages are custom-resource-classes (CRCs), so they appear in the MODX page tree: they 
are MODX pages a title, alias, template, etc. You don't need to leave them in a menu, but it is a common
on many sites to have pages where a visitor can see all the available categories and all the available terms.

If you want a taxonomy or term to be active for selection when creating or editing pages, just make sure the
taxonomy- or term-page is published.  Unpublishing the taxonomy or term will make it disappear from any page
editing menus.

Terms may be nested, e.g. a "Mammal" term may have children "Dog" and "Cat".  



## Known Problems


The GUI in the MODX resource tree is not easily customizable for the behavior this package needs, so there are
regretfully some "warts" on the UI.  For example, if you right-click on the web context, the menu shows an item
for "Create a New Term Here": but actually completing this action will fail because it is restricted on the 
backend.  Some changes to the Ext JS in the core are required before this can be fully fixed.

A Term *must* be a child of Taxonomy.  Some of the manager UI does not issue proper warnings when drag-and-drop
operations are performed with the Term- and Taxonomy-pages.  It may appear that the page has been relocated, 
but the record will not be saved in the database.  Once the MODX manager is reloaded, the page will appear to 
revert back to its original position. 



## Authors

Everett Griffiths

Copyright 2014

Official Documentation: https://github.com/craftsmancoding/taxonomies/wiki

Bugs and Feature Requests: https://github.com/craftsmancoding/taxonomies

Questions: http://forums.modx.com
