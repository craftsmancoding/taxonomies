# Taxonomies

Bringing Taxonomies to MODX Revolution: add Categories or Tags to any MODX page.

> Taxonomies are classifications like "Categories" or "Tags" that help you to organize pages on your site so they are easier to find in searches.  

<a href="https://www.youtube.com/watch?v=CFidcBQDJ0Q&feature=youtu.be" target="_blank"><img src="http://img.youtube.com/vi/CFidcBQDJ0Q/0.jpg" 
alt="Taxonomies Intro" width="480" height="360" border="10" /></a>


### Taxonomy Terms vs. Template Variables

Adding Taxonomy terms to your pages looks a lot like adding TVs to your pages, and yes, sometimes the distinction
can become blurry.  The distinction is that 

- taxonomies are intended solely to improve searching on your site
- the relationship between a page and its terms is always one-to-many

Unlike other solutions, this package uses custom tables to store the relational data so that the results
are easily (and quickly) queried.

![Taxonomies](https://raw.githubusercontent.com/wiki/craftsmancoding/taxonomies/images/resource-tree.jpg "Taxonomies in Action")

![Taxonomies on your pages](https://raw.githubusercontent.com/wiki/craftsmancoding/taxonomies/images/resource-edit.jpg "Taxonomies on your pages")


## Installation

Install this package via the MODX package manager.  See https://github.com/craftsmancoding/taxonomies/wiki/Installation

> **This package REQUIRES PHP 5.3 and MODX 2.3 or Greater**


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

- **A Taxonomy is a container for terms.**
- **A Term is unit inside a Taxonomy.** 

For example, a "Genre" Taxonomy might have Terms of "Sci-Fi" or "Romance".  A "Color" Taxonomy might include Terms
such as "Red" and "Blue".

The Taxonomy and Term pages are custom-resource-classes (CRCs), so they appear in the MODX page tree: they 
are MODX pages a title, alias, template, etc. You don't need to leave them in a menu, but it is a common
on many sites to have pages where a visitor can see all the available categories and all the available terms.

If you want a taxonomy or term to be active for selection when creating or editing pages, just make sure the
taxonomy- or term-page is published.  Unpublishing the taxonomy or term will make it disappear from any page
editing menus.

Terms may be nested, e.g. a "Mammal" term may have children "Dog" and "Cat".  



## Known Problems

**If you right-click on the web context, the menu displays the option to "Create Term Here"** 

Ideally, the menu shouldn't print this as an option, but the GUI used in the MODX resource tree is not easily
customizable (!@%$#!@ you Ext JS!).  So although that option appears in the menu, you are never allowed to complete
that action: Terms must be the children of Taxonomies or other Terms. Some changes to the Ext JS in the core are 
required before this can be fully fixed.

**The GUI issues no warning when you drag a Term into a regular folder**

Although the GUI appears to let you do this, the action is terminated on the backend.  Remember: A Term *must* be 
a child of Taxonomy OR of another Term.  It may appear that the page has been relocated, but the record will not 
be saved in the database.  Once the MODX manager is reloaded, the page will appear to revert back to its original 
position. 



## Authors

Everett Griffiths

Copyright 2014

Official Documentation: https://github.com/craftsmancoding/taxonomies/wiki

Bugs and Feature Requests: https://github.com/craftsmancoding/taxonomies

Questions: http://forums.modx.com
