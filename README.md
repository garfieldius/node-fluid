# Fluid template engine

This is a simple wrapper, for using the [fluid](https://github.com/typo3/fluid) template engine into node.js. It bundles a .phar file which will be executed as a child process, calling PHP and the given options as arguments.

## Installation

Before using it, make sure PHP 7.0 or newer is installed on the system. then install via your favorite package manager:

```
# npm
npm install --save node-fluid

# yarn
yarn add node-fluid
```

## Usage

The module `fluid` will return a "class" which can be used to render data. Fluid uses the concepts of *Layouts* and *Partials*, which must be files, relative to the working directory or as an absolute path.

First, load the module and create a new view:

```js
const fluid = require("node-fluid")
const view = new fluid();
```

Add paths to layouts and templates:

```js
view.addLayoutsPath("res/layoutes")
view.addPartialsPath("res/partials")
```

then add some variables for use in the template:

```js
view.assign("title", "Important page")
view.assign("user", {name: "John Doe", id: 1})
```

and render a template file, or directly pass in the template data.

```js
function callback(err, result) {
    if (err) {
        return console.error(err)
    }
    
    console.log("HTML: %s", result)
}

// via a path
view.renderFile("res/templates/user.html", callback)

// or pass data directly
const data = fs.readFileSync("res/templates/user.html")
view.renderData(data, callback)
```

## License

Apache License 2.0

Please see the file LICENSE, which is part of this package, or visit <https://www.apache.org/licenses/LICENSE-2.0>
