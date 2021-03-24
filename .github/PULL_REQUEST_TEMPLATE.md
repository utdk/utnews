<!--- Title format : ISSUE # : Action-verb driven description-->

## Motivation/Purpose of Changes
<!--- Why is this change needed? Links to existing issues are great. -->
Fixes #

## Resolution/Implementation
<!--- Describe any implementation choices you made that are noteworthy -->
<!--- or may require discussion. -->

## Screenshot(s)
<!--- (If relevant) -->

## Types of changes
<!--- Put an `x` in all that apply: -->
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change

## Checklist:
<!--- Go over all the following points, and put an `x` in all the boxes that apply. -->
<!--- If you're unsure about any of these, don't hesitate to ask. We're here to help! -->
<!--- Put an `x` in all the boxes that apply: -->
- [ ] Automated tests pass <!--- If tests don't pass because of a known reason, elaborate on the test and issue -->
- [ ] Code meets syntax standards
- [ ] Namespacing follows [team conventions](https://github.austin.utexas.edu/eis1-wcs/d8-standards/blob/master/Naming_Conventions.md)
- [ ] Change requires a change to the documentation.
  - [ ] I have updated the documentation accordingly.
- [ ] I have added tests to cover my changes.

## Reference: installing a site with this feature
<!--- Include notes for both functional testing & code review -->
0. `git clone git@github.austin.utexas.edu:eis1-wcs/utdk_scaffold.git && cd utdk_scaffold`
0. `composer require utexas/utdk_profile:dev-<BRANCH-NAME>`
0. `composer run-script dev-scaffold`
0. `fin init && fin init-site --wcs`
0. `composer require utexas/utnews:dev-<BRANCH-NAME>`
0. `fin drush en utnews -y`

## Reference: running tests locally
0. `fin test web/modules/contrib/utnews/tests/src/Functional`
0. `fin test-js web/modules/contrib/utnews/tests/src/FunctionalJavascript`

## Acceptance Criteria
<!-- Copy or link to acceptance criteria in issue -->
