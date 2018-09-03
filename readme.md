#Laravel HelpDesk Server
###The basic structure use the following dependencies, please check it before start coding:

```
http://esbenp.github.io/2016/04/11/modern-rest-api-laravel-part-0/
https://packagist.org/packages/optimus/heimdal
https://github.com/esbenp/bruno
https://github.com/esbenp/genie
```
###**Command to deploy to staging**
```
git push staging develop:master
```

```
git push production master
```

#####In order to get this commands working add the following origins to the git configuration.
```
-- Developer --
https://git.heroku.com/zendesk-channel-server-dev.git
-- Staging --
https://git.heroku.com/zendesk-channel-server-staging.git

```

#####In order to build the frontend to be committed execute the following command inside the Frontend folder.
```
ng build --prod --build-optimizer
```

### initial secret to horizon

assuresoft-bolivia