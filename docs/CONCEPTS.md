# Craft3 Base

## Main settings files

- The `.env` is only used for initial configuration and should be deleted after the installation.
- The `.env.php` file is your main website environment config. State, Database, Keys, etc...
- The `.env.sh` is the Shell scripts config file. Production/Staging server configuration for assets synch'in.

## Config

- Main config files for Craft 3 CMS.

## Docs folder

- The Docs folder is there to help you understand how to use this Craft 3 CMS Base.

## Src Folder

- Sources files. This is where the development happen.

## Template Folder

- Your Twig files. Basically 'views' used by Craft 3 CMS ans Routing.

## Scripts folder

- Contain our Shell Script logic. Should never be edited. Can be ignored by your editor.

## Build folder

- Temporary build folder used by Gulp. If needed, this can host generated html components from a Framework such as VueJs, ReactJs, etc... to be able to use it in pure ES6.

## Some tools used in this project that worth mentioning

#### Livereload
- https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei
- If you want to use it with local files, be sure to enable “Allow access to file URLs” checkbox in Tools > Extensions > LiveReload after installation.

#### Fontello
- The fonts task first gen­er­ates a cus­tom icon font via fontel­lo via a config.json file that con­tains only the glyphs we need.
- Import and generate fonts with http://fontello.com/
- Get Config only
- Replace `config.json` in the `src/fontello` folder
- `gulp build`

#### Generate Favicons
- The favicons task gen­er­ates the myr­i­ad of web­site fav­i­cons from a sin­gle source image, and also gen­er­ates the HTML nec­es­sary to display/​include them. This makes it super easy to gen­er­ate all of the var­i­ous fav­i­con for­mats that I hon­est­ly have a hard time keep­ing up with.
- `gulp build`

#### Images optimisations
- The imagemin task opti­mizes all of the images in your pkg.paths.dist.img glob in situ. These are images that are part of the site itself, and are checked into your git repo. For images that the client will upload, they should be opti­mized serv­er-side.
- `gulp build`

#### Critical CSS
- The criticalcss task gen­er­ates our Crit­i­cal CSS that has the styles need­ed to ren­der our ​“above the fold con­tent”. This is for page speed optimisation.
- `gulp build`

#### Accessibility Tests
- The a11y task runs an acces­si­bil­i­ty audit on all of our web­site tem­plates. I.e: W3C Validation and more...
- `gulp a1ly`

#### Download 3d pary JS
- The down­load task down­loads third par­ty JavaScript (such as Google Ana­lyt­ics) that I want to serve myself, so I have con­trol over the expires head­er.
- `TBD`
