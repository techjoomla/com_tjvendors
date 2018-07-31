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