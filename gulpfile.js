// jshint esversion: 6
// jshint node: true
"use strict";

// package.json variables
const pkg = require("./package.json");

// Imports
const notify = require('gulp-notify');
const fancyLog = require('fancy-log');
const chalk = require('chalk');
const critical = require('critical');

const gulp = require('gulp');
const plumber = require('gulp-plumber');
const livereload = require('gulp-livereload');
const autoprefixer = require('gulp-autoprefixer');
const cached = require('gulp-cached');
const concat = require('gulp-concat');
const cssnano = require('gulp-cssnano');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const gulpif = require('gulp-if');
const size = require('gulp-size');
const newer = require('gulp-newer');
const print = require('gulp-print').default;
const header = require('gulp-header');
const filter = require('gulp-filter');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const rename = require('gulp-rename');
const replace = require('gulp-replace');
const favicons = require('gulp-favicons');
const imagemin = require('gulp-imagemin');

/**
 * Notifications
 */
 notify.logLevel(0);
 
 const onError = function(err) {
 
   notify.onError({
     sound:   'Beep',
     title:   'Gulp error',
     message: '<%= error.message %>',
   })(err);
 
   console.log(err.messageFormatted);
 
   this.emit('end');
 
 };
 

// Our banner
const banner = (function() {
  let result = "";
  try {
    result = [
      "/**",
      " * @project        <%= pkg.name %>",
      " * @author         <%= pkg.author %>",
      " * @build          " + moment().format("llll") + " ET",
      " * @release        " + gitRevSync.long() + " [" + gitRevSync.branch() + "]",
      " * @copyright      Copyright (c) " + moment().format("YYYY") + ", <%= pkg.copyright %>",
      " *",
      " */",
      ""
    ].join("\n");
  }
  catch (err) {
  }
  return result;
})();

/*
  Task: scss
  Description: Build the scss to the build folder,
  including the required paths, and writing out a sourcemap
*/
gulp.task("scss", () => {
  fancyLog("-> Compiling scss");
  return gulp.src(pkg.paths.src.scss + pkg.vars.scssName)
    .pipe(plumber({errorHandler: onError}))
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(sass({
      includePaths: pkg.paths.scss
    })
      .on("error", sass.logError))
    .pipe(cached("sass_compile"))
    .pipe(autoprefixer({ overrideBrowserslist: ['last 2 versions', 'ie 9-11'], cascade: false }))
    .pipe(sourcemaps.write("./"))
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.build.css));
});

/*
  Task: css
  Description: Combine & minimize any distribution CSS into
  the public css folder, and add our banner to it
*/
gulp.task("css", gulp.series("scss", () => {
  fancyLog("-> Building css");

  return gulp.src(pkg.globs.distCss)
    .pipe(plumber({errorHandler: onError}))
    .pipe(newer({dest: pkg.paths.dist.css + pkg.vars.siteCssName}))
    .pipe(print())
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(concat(pkg.vars.siteCssName))
    .pipe(gulpif(process.env.NODE_ENV === "production",
      cssnano({
        discardComments: {
          removeAll: true
        },
        discardDuplicates: true,
        discardEmpty: true,
        minifyFontValues: true,
        minifySelectors: true
      })
    ))
    .pipe(header(banner, {pkg: pkg}))
    .pipe(sourcemaps.write("./"))
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.dist.css))
    .pipe(filter("**/*.css"))
    .pipe(livereload());
})
);

/*
  Task: js-vendors
  Description: Takes all the /src/js/vendors/ js files
  and brings it to the /build/js/custom/
*/
gulp.task("js-vendors", () => {
  fancyLog("-> Transpiling Vendors...");
  return gulp.src(pkg.globs.jsVendors)
    .pipe(plumber({errorHandler: onError}))
    .pipe(gulp.dest(pkg.paths.build.customJs));
});

/*
  Task: custom-js-babel
  Description: Transpile our Javascript into the build directory
*/
gulp.task("custom-js-babel", gulp.series("js-vendors", () => {
  fancyLog("-> Transpiling Custom Javascript via Babel...");
  return gulp.src(pkg.globs.babelCustomJs)
    .pipe(plumber({errorHandler: onError}))
    .pipe(newer({dest: pkg.paths.build.customJs}))
    .pipe(babel())
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.build.customJs));
}));

/*
  Task: custom-js
  Description: Combine & minimize any custom JS into
  the public js folder, and add our banner to it
*/
gulp.task("custom-js", gulp.series("custom-js-babel", () => {
  fancyLog("-> Building site JS");
  return gulp.src(pkg.globs.distCustomJs)
    .pipe(plumber({errorHandler: onError}))
    .pipe(newer({dest: pkg.paths.dist.customJs + pkg.vars.siteJsName}))
    .pipe(print())
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(uglify())
    .pipe(concat(pkg.vars.siteJsName))
    .pipe(header(banner, {pkg: pkg}))
    .pipe(sourcemaps.write("./"))
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.dist.js))
    .pipe(filter("**/*.js"))
    .pipe(livereload());
}));

/*
  Task: js-babel
  Description: Transpile our Javascript into the build directory
*/
gulp.task("js-babel", gulp.series("custom-js", () => {
  fancyLog("-> Transpiling Javascript via Babel...");
  return gulp.src(pkg.globs.babelJs)
    .pipe(plumber({errorHandler: onError}))
    .pipe(newer({dest: pkg.paths.build.js}))
    .pipe(babel())
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.build.js));
}));

/*
  Task: js-inline
  Description: Minimize the inline Javascript into
  _inlinejs in the templates path
*/
gulp.task("js-inline", gulp.series("js-babel", () => {
  fancyLog("-> Copying inline js");
  return gulp.src(pkg.globs.inlineJs)
    .pipe(plumber({errorHandler: onError}))
    .pipe(gulpif(["*.js", "!*.min.js"],
      newer({dest: pkg.paths.templates + "_inlinejs", ext: ".min.js"}),
      newer({dest: pkg.paths.templates + "_inlinejs"})
    ))
    .pipe(gulpif(["*.js", "!*.min.js"],
      uglify()
    ))
    .pipe(gulpif(["*.js", "!*.min.js"],
      rename({suffix: ".min"})
    ))
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.templates + "_inlinejs"))
    .pipe(filter("**/*.js"));
}));

/*
  Task: js
  Description: Minimize any distribution Javascript into
  the public js folder, and add our banner to it
*/
gulp.task("js", gulp.series("js-inline", () => {
  fancyLog("-> Building js");

  let pkgGlobsDistJs = pkg.globs.distJsDev;
  if (process.env.NODE_ENV === "production") {
    pkgGlobsDistJs = pkg.globs.distJs;
  }

  return gulp.src(pkg.globs.distJs)
    .pipe(plumber({errorHandler: onError}))
    .pipe(gulpif(["*.js", "!*.min.js"],
      newer({dest: pkg.paths.dist.js, ext: ".min.js"}),
      newer({dest: pkg.paths.dist.js})
    ))
    .pipe(gulpif(["*.js", "!*.min.js"],
      uglify()
    ))
    .pipe(gulpif(["*.js", "!*.min.js"],
      rename({suffix: ".min"})
    ))
    .pipe(header(banner, {pkg: pkg}))
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.dist.js))
    .pipe(filter("**/*.js"))
    .pipe(livereload());
}));

/*
  Task: static-assets-version
  Description: Increment the static version of assets in .env
*/
gulp.task("static-assets-version", async () => {
  gulp.src("./.env")
    .pipe(replace(/STATIC_ASSETS_VERSION=(\d+)/g, function(match, p1, offset, string) {
      p1++;
      fancyLog("-> Changed staticAssetsVersion to " + p1);
      return "STATIC_ASSETS_VERSION=" + p1;
    }))
    .pipe(gulp.dest('./'));
});

/*
  Task: code-version
  Description: Increment the code version in .env
*/
gulp.task("code-version", async () => {
  gulp.src("./.env")
    .pipe(replace(/CODE_VERSION=(\d+)/g, function(match, p1, offset, string) {
      p1++;
      fancyLog("-> Changed Code Version to " + p1);
      return "CODE_VERSION=" + p1;
    }))
    .pipe(gulp.dest('./'));
});

/*
  Function: doSynchronousLoop
  Description: Process data in an array synchronously,
  moving onto the n+1 item only after the nth item callback
*/
const doSynchronousLoop = (data, processData, done) => {
  if (data.length > 0) {
    const loop = (data, i, processData, done) => {
      processData(data[i], i, () => {
        if (++i < data.length) {
          loop(data, i, processData, done);
        } else {
          done();
        }
      });
    };
    loop(data, 0, processData, done);
  } else {
    done();
  }
}

/*
  Function: processCriticalCSS
  Description: Process the critical path CSS one at a time
*/
const processCriticalCSS = (element, i, callback) => {
  const criticalSrc = pkg.urls.critical + element.url;
  const criticalDest = pkg.paths.templates + element.template + "_critical.min.css";

  let criticalWidth = 1200;
  let criticalHeight = 1200;
  if (element.template.indexOf("amp_") !== -1) {
    criticalWidth = 600;
    criticalHeight = 19200;
  }
  fancyLog("-> Generating critical CSS: " + chalk.cyan(criticalSrc) + " -> " + chalk.magenta(criticalDest));
  critical.generate({
    src: criticalSrc,
    dest: criticalDest,
    penthouse: {
      blockJSRequests: false,
      forceInclude: pkg.globs.criticalWhitelist
    },
    inline: false,
    ignore: [],
    css: [
      pkg.paths.dist.css + pkg.vars.siteCssName,
    ],
    minify: true,
    width: criticalWidth,
    height: criticalHeight
  }, (err, output) => {
    if (err) {
      fancyLog(chalk.magenta(err));
    }
    callback();
  });
}

/*
  Function: processAccessibility
  Description: Run pa11y accessibility tests on each template
*/
const processAccessibility = (element, i, callback) => {
  const accessibilitySrc = pkg.urls.critical + element.url;
  const cliReporter = require("./node_modules/pa11y/reporter/cli.js");
  const options = {
    log: cliReporter,
    ignore:
      [
        "notice",
        "warning"
      ],
  };
  const test = pa11y(options);

  fancyLog("-> Checking Accessibility for URL: " + chalk.cyan(accessibilitySrc));
  test.run(accessibilitySrc, (error, results) => {
    cliReporter.results(results, accessibilitySrc);
    callback();
  });
}

/*
  Task: a11y
  Description: Process pa11y accessibility tests
*/
gulp.task("a11y", (callback) => {
  doSynchronousLoop(pkg.globs.critical, processAccessibility, () => {
    // all done
    callback();
  });
});

/*
  Task: favicons-generate
  Description: Generate favicons assets
*/
gulp.task("favicons-generate", () => {
  fancyLog("-> Generating favicons");
  return gulp.src(pkg.paths.favicon.src).pipe(favicons({
    appName: pkg.name,
    appDescription: pkg.description,
    developerName: pkg.author,
    developerURL: pkg.urls.live,
    background: "#FFFFFF",
    path: pkg.paths.favicon.path,
    url: pkg.site_url,
    display: "standalone",
    orientation: "portrait",
    version: pkg.version,
    logging: false,
    online: false,
    html: pkg.paths.build.html + "favicons.html",
    replace: true,
    icons: {
      android: false, // Create Android homescreen icon. `boolean`
      appleIcon: true, // Create Apple touch icons. `boolean`
      appleStartup: false, // Create Apple startup images. `boolean`
      coast: true, // Create Opera Coast icon. `boolean`
      favicons: true, // Create regular favicons. `boolean`
      firefox: true, // Create Firefox OS icons. `boolean`
      opengraph: false, // Create Facebook OpenGraph image. `boolean`
      twitter: false, // Create Twitter Summary Card image. `boolean`
      windows: true, // Create Windows 8 tile icons. `boolean`
      yandex: true, // Create Yandex browser icon. `boolean`
    },
  })).pipe(gulp.dest(pkg.paths.favicon.dest));
});

/*
  Task: favicons
  Description: Process and move favicons assets
*/
gulp.task("favicons", gulp.series("favicons-generate", () => {
  fancyLog("-> Copying favicon.ico");
  return gulp.src(pkg.globs.siteIcon)
    .pipe(size({gzip: true, showFiles: true}))
    .pipe(gulp.dest(pkg.paths.dist.base));
}));

/*
  Task: imagemin
  Description: Optimize images
*/
gulp.task("imagemin", () => {
  fancyLog("-> Minimizing images in " + pkg.paths.src.img);
  return gulp.src(pkg.paths.src.img + "**/*.{png,jpg,jpeg,gif,svg}")
    .pipe(imagemin({
      progressive: true,
      interlaced: true,
      optimizationLevel: 7,
      svgoPlugins: [{removeViewBox: false}],
      verbose: true,
      use: []
    }))
    .pipe(gulp.dest(pkg.paths.dist.img));
});

/*
  Task: fonts
  Description: Copy fonts files
*/
gulp.task("fonts", () => {
  return gulp.src(pkg.globs.fonts)
    .pipe(gulp.dest(pkg.paths.dist.fonts));
});

/*
  Task: templates
  Description: Set livereload
*/
gulp.task("templates", () => {
  return gulp.src(pkg.paths.templates)
    .pipe(plumber({errorHandler: onError}))
    .pipe(livereload());
});

/*
  Task: translations
  Description: Set livereload
*/
gulp.task("translations", () => {
  return gulp.src(pkg.paths.translations)
    .pipe(plumber({errorHandler: onError}))
    .pipe(livereload());
});

/*
  Task: generate
  Description: Generate all assets files
*/
gulp.task("generate", gulp.series("favicons", "fonts", "imagemin", async () => {
  fancyLog("-> Generating app assets");
}));

/*
  Task: criticalcss
  Description: Generate all critical files
*/
gulp.task("criticalcss", gulp.series("css", "generate", (callback) => {
  doSynchronousLoop(pkg.globs.critical, processCriticalCSS, () => {
    // all done
    callback();
  });
}));

/*
  Task: set-dev-node-env
  Description: Set the node environment to development
*/
gulp.task("set-dev-node-env", async () => {
  fancyLog("-> Setting NODE_ENV to development");
  return process.env.NODE_ENV = "development";
});

/*
  Task: serve
  Description: Set the node environment to development,
  processing and watching all assets files
*/
gulp.task("serve", gulp.series("set-dev-node-env", "css", "js", async () => {
    fancyLog("-> Livereload listening for changes");
    livereload.listen();
    gulp.watch([pkg.paths.src.scss + "**/*.scss"], gulp.series("css"));
    gulp.watch([pkg.paths.src.css + "**/*.css"], gulp.series("css"));
    gulp.watch([pkg.paths.src.js + "**/*.js"], gulp.series("js"));
    gulp.watch([pkg.paths.templates + "**/*.{html,htm,twig,php}"], gulp.series("templates"));
    gulp.watch([pkg.paths.translations + "**/*.{html,htm,twig,php}"], gulp.series("translations"));
  })
);

/*
  Task: set-prod-node-env
  Description: Set the node environment to production
*/
gulp.task("set-prod-node-env", async () => {
  fancyLog("-> Setting NODE_ENV to production");
  return process.env.NODE_ENV = "production";
});

/*
  Task: build:fast
  Description: Set the node environment to production,
  processing and building all style and script files
*/
gulp.task("build:fast", gulp.series("set-prod-node-env", "static-assets-version", "css", "js"));

/*
  Task: build
  Description: Set the node environment to production,
  processing and building all assets files
*/
gulp.task("build", gulp.series("set-prod-node-env", "static-assets-version", "css", "generate", "js"));
