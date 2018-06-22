
/*
 * (c) 2018 by Georg Großberger <contact@grossberger-ge.org>
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the Apache License 2.0
 *
 * For the full copyright and license information see
 * <https://www.apache.org/licenses/LICENSE-2.0>
 */

const proc = require("child_process");
const path = require("path");

function Fluid () {

    this.variables = {};

    this.templatePaths = [];
    this.partialPaths = [];
    this.layoutPaths = [];

    this._makeOpts = function(name, value) {
        const opts = {
            templates: this.templatePaths,
            partials: this.partialPaths,
            layouts: this.layoutPaths,
            vars: this.variables
        };

        opts[name] = value;
        return opts;
    };

    this.addTemplatesPath = function (path) {
        this.templatePaths.push(path);
    };

    this.addPartialsPath = function (path) {
        this.partialPaths.push(path);
    };

    this.addLayoutsPath = function (path) {
        this.layoutPaths.push(path);
    };

    this.assign = function (name, value) {
        this.variables[name] = value;
    };

    this.assignMultiple = function (values) {
       Object.keys(values).forEach((name) => {
           this.assign(name, values[value])
       })
    };

    this.renderFile = function (file, cb) {
        render(this._makeOpts("source", file), cb)
    };

    this.renderData = function (data, cb) {
        render(this._makeOpts("data", data), cb)
    };
}

module.exports = Fluid;
module.exports.php = "php";
module.exports.spawnOpts = {
    cwd: process.cwd(),
    env: process.env
};

function render(opts, cb) {
    const phar = path.join(__dirname, "fluid.phar");
    const args = ["-f", phar, "--"];
    const paths = [
        [opts.templates, "--templatesPath"],
        [opts.partials, "--partialsPath"],
        [opts.layouts, "--layoutsPath"]
    ];

    paths.forEach((part) => {
        const prop = part[1];

        part[0].forEach((dir) => {
            args.push(prop)
            args.push(dir)
        })
    });

    args.push("--variables");
    args.push(JSON.stringify(opts.vars));

    if (opts.source) {
        args.push("--source");
        args.push(opts.source);
    }

    const options = {
        cwd: module.exports.spawnOpts.cwd,
        env: module.exports.spawnOpts.env,
        shell: true
    };

    const fluid = proc.spawn(module.exports.php, args, options);
    const chunks = [];
    let err = null;

    fluid.stdout.on("data", (data) => {
        chunks.push(data);
    });

    fluid.stderr.on("data", (data) => {
        err = err + data;
    });

    fluid.on("close", () => {
        cb(err, Buffer.from(chunks.join("")));
    });

    if (opts.data) {
        fluid.stdin.write(opts.data);
        fluid.stdin.end();
    }
}
