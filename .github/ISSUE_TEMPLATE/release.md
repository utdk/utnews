---
name: Release
about: Prepare code for a new release
labels: 'release'

---

## Pre-release checks

- [ ] Review the documentation issues for any pending tasks that relate to the issues resolved; if any have not been completed, put this issue on hold & resolve those documentation tasks
- [ ] Contributed module dependencies have been updated, if updates are available

## Release pull request tasks

- [ ] Create release branch from develop, e.g. `release/1.0.0-alpha.3`
- [ ] Bump version number in `utevent.info.yml`
- [ ] Open PR for release branch

## Release completion tasks

- [ ] After approval, merge release branch to develop & master:
- Merge using the Gitflow strategy:
    - `git fetch && git checkout develop && git pull origin develop && git merge --no-ff release/<version number>`
    - `git fetch && git checkout master && git pull origin master && git merge --no-ff release/<version number>`
    - `git tag -a <version number>`
    - `git push origin develop && git push origin master && git push origin <version number>`
- [ ] [Create a new release](https://github.austin.utexas.edu/eis1-wcs/utevent/releases/new) (version number and release title should be the same (e.g., `1.0.0-alpha.3`)
- [ ] Use [gren](https://github.com/github-tools/github-release-notes) generate the release notes `gren release --api-url=https://github.austin.utexas.edu/api/v3 --repo=utevent --username=eis1-wcs --ignore-issues-with="wontfix,release,duplicate,invalid" --override`
