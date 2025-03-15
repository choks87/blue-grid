# Blue-Grid Assessment

To Set Up everything run, only once:
```bash
sudo make setup
```

Then run (and every next time you work with project):
```bash
sudo make up
```

To attach to container:
```bash
sudo make attach
```

To run server, so you can use and test:
```bash
sudo make start
```
Then go to http://127.0.0.1/docs and try API.
Or click here for 3 mentioned endpoints:
http://localhost/api/files-and-directories
http://localhost/api/directories
http://localhost/api/files

You can apply pagination query params, foe ex.
http://localhost/api/directories?page=1&per_page=5,
but you cannot exceed 100, as you can see here:
http://localhost/api/directories?page=1&per_page=101

For Production environment:
```bash
make start-prod
```

To run tests and static .analysis, once you are attached:
```bash
sudo make test
sudo make code-check
```
