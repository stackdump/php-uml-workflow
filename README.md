## Uml Workflow POC

This archive contains a sample module named 'CMS_Workflow' organized using the zend package naming schema.
It relies on several other proprietary libraries that are not included (and hence will not work as is).

### Purpose:

  The main purpose of this project was to develop a generic event-driven workflow library controlled from a single config file.
  This allows for the documentation, sequence control, and guard logic to be maintained in a single file modified by a UML editor.

### Status - non-working & abandoned

  This module was created in conjunction with another project. Not much time was spent refactoring for efficiency.
  If time permitted, I would have finished updating the Workflow object to allow it to be serialized and then woken up 

### UML Files:

The included samples were created using: ArogoUML v 0.28

You should be able to start a compatible client using this web start link: 
http://argouml-downloads.tigris.org/jws/argouml-0.28.1.jnlp

OR

Visiting the download page: 
http://argouml-downloads.tigris.org/

#### Subdirectories

    /code - php source code from which the documentation is generated
    /data - sample .uml files containing workflow diagrams
    /docs - the phpDocumentor generated files

