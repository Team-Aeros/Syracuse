#!/usr/bin/env bash
cd ../
git fetch upstream
git merge upstream/master
git push