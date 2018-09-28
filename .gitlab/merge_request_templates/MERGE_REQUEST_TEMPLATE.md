**Hygiene checks 1**

- [ ] Have you followed [commit message formats](http://git.tekdi.net/snippets/24)?
- [ ] Have you ensured, there are no PHP syntax errors?
- [ ] Have you done PHPCS for PHP?
- [ ] Have you done jslint for Javascript?
- [ ] Do the MR has meaningful title?
- [ ] Do the MR has meaningful description stating #issueId issue title?

**Hygiene checks 2**

- [ ] Have you ensured variables are named as:

  - in PHP => variableName
  - in CSS => variable-name
  - in JS => variableName

- [ ] Have you ensured logical names are given to:
  - functions
  - classes

- [ ] Have you ensured there are no spelling mistakes in;
  - variable names
  - function names
  - class names
  - language constants

- [ ] Have you ensured language constants are used in:
  - in PHP
  - in JS

- [ ] Have you maintained case convention across all views? 
   - e.g. either `Sentence case`
   - OR `All First Letters are Capital for Words Having More Than 3 Characters`

- [ ] Have you ensured
  - labels are meaningful
  - tooltips are meaningful

**Code**

- [ ]  Have you added comments for complex logic?
  - PHP code `// Single line` and `/*Multiline comment**/`
  - JS code `/*Single line comment*/` or `/*Multiline comment*/`
  
- [ ]  have you followed correct Joomla MVC?
  - There is no logic + data operations in layout / view files
  - All data is collected in controller and passed to model
  - All business logic is in models

**ACL checks**
- [ ] Have you added ACL checks for edit?
- [ ] Have you added ownership checks for edit?
- [ ] Have you added ACL checks for view record?
- [ ] Have you added ownership checks for view record?

**Security checks**
- [ ] Have you added CSRF tokens wherever required? [refer](https://docs.joomla.org/How_to_add_CSRF_anti-spoofing_to_forms)
- [ ] Have you used query builder for database operations? [refer - Selecting data using JDatabase](https://docs.joomla.org/Selecting_data_using_JDatabase) & [refer - Inserting, Updating and Removing data using JDatabase](https://docs.joomla.org/Inserting,_Updating_and_Removing_data_using_JDatabase)
- [ ] Have you added client side validations? [read more](/Client-side_form_validation)
- [ ] Have you added server side validations? [read more](https://docs.joomla.org/Server-side_form_validation)
- [ ] Have you escaped / sanitized your input and output parameter?  [read more](https://docs.joomla.org/Secure_coding_guidelines)
- [ ] Have you added checks for Open redirection? [read more](https://www.owasp.org/index.php/Unvalidated_Redirects_and_Forwards_Cheat_Sheet)
- [ ] Have you added validations on file upload?

# Reference links for addressing security issues

### SQL injection 

- http://php.net/manual/en/security.database.sql-injection.php
- http://www.owasp.org/index.php/PHP_Top_5#P3:_SQL_Injection

### XSS 

- http://en.wikipedia.org/wiki/Cross-site_scripting
- http://www.owasp.org/index.php/Cross_Site_Scripting
- http://www.owasp.org/index.php/PHP_Top_5#P2:_Cross-site_scripting
- http://php.net/manual/en/function.htmlspecialchars.php

### File upload

- https://blog.qualys.com/securitylabs/2015/10/22/unrestricted-file-upload-vulnerability
- https://www.computerweekly.com/answer/File-upload-security-best-practices-Block-a-malicious-file-upload