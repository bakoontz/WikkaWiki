# WikkaWiki Tests

## Overview
Wikka test were introduced with the transfer of the Wikka source code to Github. They are meant to serve the following goals:

- Provide a basis for effective collaboration
- Avoid regressions and eliminate bugs
- Document new bugs and prove they are fixed
- Signal the correctness of new code
- Improve overall code design and quality


## Setup
Wikka tests require PhpUnit to be installed. For installation instructions, see [the PhpUnit docs](http://phpunit.de/manual/3.7/en/installation.html).

I found the PEAR method simple and straightforward.

Tests require a mysql database connection. Update the configuration file provided in the test directory by first copying the dist version:

    cp -v test/test.config.php{-dist,}
    
Then manually update the file `test/test.config.php`.



## To Run
Tests are run from the command line using `phpunit`. From the root WikkaWiki directory, run the following command:

    phpunit test
    
You should see something like this:

    PHPUnit 3.7.28 by Sebastian Bergmann.
    
    I..IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII  63 / 137 ( 45%)
    IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII 126 / 137 ( 91%)
    IIIIIIIIII.
    
    Time: 208 ms, Memory: 11.25Mb
    
    OK, but incomplete or skipped tests!
    Tests: 137, Assertions: 3, Incomplete: 134.


## To Do
There is much to be done. Some areas that need to be addressed:

### Data Fixtures
Currently, the tests will create a database, since the main Wakka class expects it. But it does not create any tables. One of the next steps will be to add a setup process for data fixtures that creates tables and inserts test data records. 

### Code Organization
Working with tests will quickly illustrate some obvious ways by which refactoring the Wikka code could both improve code modularity and facilitate testing. The main Wikka class itself would probably make more sense as a collection of classes, or a composite class that encapsulated smaller more coherent classes.

Similarly, the setup/install process could be refactored so that it could also be leverage by tests. Currently, it creates all tables. But the table sql syntax is currently embedded as string literals, which makes them accessible outside the install script. By reorganizing the install script into more functional code unit and assigning the sql commands to a reusable data structure, these information would be accessible elsewhere like tests where it would be quite useful.

### Test Helpers
At some point, a simple library of helper functions in the test directory, in addition to those provided by PhpUnit, would be useful.

### Missing Tests
The easiest way to improve testing in WikkaWiki is to write new tests and include them with any pull requests. The DevTest script in the test directory root is intended to provided both a working reference for creating texts and a sandbox for experimenting with various testing concepts.


## Comments / Questions
Feel free to reach out to me, Tom, on Github at [klenwell@gmail.com](https://github.com/klenwell)
